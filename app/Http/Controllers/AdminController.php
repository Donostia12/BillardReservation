<?php

namespace App\Http\Controllers;

use App\Models\BilliardTable;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Cleanup expired orders first
        $this->cleanupExpiredOrders();

        // Fetch tables with active orders
        $tables = BilliardTable::with(['orders' => function ($query) {
            $query->whereIn('status', ['active', 'process', 'pending'])
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->latest();
        }])->get();

        // Map status and expires_at for each table
        foreach ($tables as $table) {
            $currentOrder = $table->orders->first();
            if ($currentOrder) {
                if ($currentOrder->status === 'active') {
                    $table->status = 'used';
                } elseif ($currentOrder->status === 'process') {
                    $table->status = 'reserved';
                    $table->expires_at = $currentOrder->expires_at;
                    $table->order_status = 'process'; // Keep original status
                } elseif ($currentOrder->status === 'pending') {
                    $table->status = 'pending';
                    $table->expires_at = $currentOrder->expires_at;
                    $table->order_status = 'pending';
                }
            } else {
                $table->status = 'available';
            }
        }

        // Fetch orders needing verification (process status)
        $pendingOrders = Order::whereIn('status', ['pending', 'process'])
            ->with(['billiardTable', 'user'])
            ->latest()
            ->get();

        // Map orders for frontend
        $ordersData = $pendingOrders->map(function ($o) {
            return [
                'id' => $o->id,
                'table_number' => $o->billiardTable->number,
                'user_name' => $o->user->name,
                'status' => $o->status,
                'amount' => $o->amount,
                'expires_at' => $o->expires_at?->timestamp,
                'created_at' => $o->created_at->timestamp,
                'payment_proof' => $o->payment_proof ? asset('storage/' . $o->payment_proof) : null,
            ];
        });

        return view('admin.dashboard', compact('tables', 'pendingOrders', 'ordersData'));
    }

    public function getPendingOrders()
    {
        // API endpoint for polling
        $orders = Order::whereIn('status', ['pending', 'process'])
            ->with(['billiardTable', 'user'])
            ->latest()
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'table_number' => $order->billiardTable->number,
                    'user_name' => $order->user->name,
                    'status' => $order->status,
                    'amount' => $order->amount,
                    'expires_at' => $order->expires_at?->timestamp,
                    'created_at' => $order->created_at->timestamp,
                    'payment_proof' => $order->payment_proof ? asset('storage/' . $order->payment_proof) : null,
                ];
            });

        return response()->json([
            'count' => $orders->count(),
            'orders' => $orders,
        ]);
    }

    public function approveOrder(Order $order)
    {
        $order->update([
            'status' => 'active',
            'expires_at' => null, // Clear expiry for active orders
        ]);

        return back()->with('success', 'Order approved successfully!');
    }

    public function rejectOrder(Order $order)
    {
        $order->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Order rejected.');
    }

    private function cleanupExpiredOrders()
    {
        Order::whereIn('status', ['pending', 'process'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['status' => 'cancelled']);
    }
}
