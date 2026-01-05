<?php

namespace App\Http\Controllers;

use App\Models\BilliardTable;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    private function cleanupExpiredOrders()
    {
        // Cancel pending/process orders that have expired
        Order::whereIn('status', ['pending', 'process'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['status' => 'cancelled']);
    }

    public function index()
    {
        $this->cleanupExpiredOrders();

        // Eager load active/pending orders to determine status
        $tables = BilliardTable::with(['orders' => function ($query) {
            $query->whereIn('status', ['active', 'process', 'pending'])
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->latest();
        }])->get()->keyBy('number');

        // Set Timezone
        $timezone = config('app.timezone');

        // Dynamically update status for display
        foreach ($tables as $table) {
            $currentOrder = $table->orders->first();
            if ($currentOrder) {
                // Map order status to table status
                if ($currentOrder->status === 'active') {
                    $table->status = 'used'; // or 'being used'
                } elseif ($currentOrder->status === 'process') {
                    $table->status = 'reserved';
                    $table->expires_at = $currentOrder->expires_at;
                } elseif ($currentOrder->status === 'pending') {
                    $table->status = 'pending';
                    $table->expires_at = $currentOrder->expires_at;
                }
            } else {
                $table->status = 'available';
            }
        }

        $layoutMap = [
            'row1' => [1, 2, 3, 4, 5, 6, 7],
            'row2' => [
                'left' => [8, 9, 10, 11, 12, 14, 15, 16],
                'right' => [23, 24, 25, 26]
            ],
            'row3' => [
                'left' => [18, 17],
                'right' => [27, 28, 29]
            ],
            'row4' => [
                'left' => [22, 21, 20, 19],
                'right' => [30, 31]
            ]
        ];

        return view('booking.index', compact('tables', 'layoutMap', 'timezone'));
    }

    public function create($number)
    {
        $this->cleanupExpiredOrders();

        $table = BilliardTable::where('number', $number)->firstOrFail();

        // Check availability
        $isReserved = Order::where('billiard_table_id', $table->id)
            ->whereIn('status', ['active', 'process', 'pending'])
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();

        if ($isReserved) {
            return redirect()->route('booking.index')->with('error', 'Table is currently reserved.');
        }

        return view('booking.create', compact('table'));
    }

    public function store(Request $request)
    {
        $this->cleanupExpiredOrders();

        $request->validate([
            'table_id' => 'required|exists:billiard_tables,id',
            'start_time' => 'required|date',
            'duration' => 'required|integer|min:1',
        ]);

        $table = BilliardTable::findOrFail($request->table_id);
        $startTime = Carbon::parse($request->start_time);
        $endTime = $startTime->copy()->addHours((int) $request->duration);

        // Check for overlaps
        // Overlap logic: (StartA <= EndB) and (EndA >= StartB)
        // With Status Constraint AND Expiry Constraint
        $exists = Order::where('billiard_table_id', $table->id)
            ->whereIn('status', ['active', 'process', 'pending'])
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $startTime)
                            ->where('end_time', '>', $endTime);
                    });
            })
            ->exists();

        if ($exists) {
            return back()->withErrors(['start_time' => 'This time slot is already booked or pending payment.']);
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'billiard_table_id' => $table->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'amount' => 50000 * (int) $request->duration,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(15), // Set 15 min expiration
        ]);

        Notification::create([
            'user_id' => auth()->id(),
            'title' => 'Booking Created',
            'message' => "Please upload payment proof for Table {$table->number} within 15 minutes.",
        ]);

        return redirect()->route('dashboard')->with('success', 'Booking created! Please upload payment proof within 15 minutes.');
    }

    public function uploadProof(Request $request, Order $order)
    {
        $this->cleanupExpiredOrders();


        if ($order->status === 'cancelled') {
            return back()->with('error', 'Booking has expired.');
        }

        $request->validate([
            'payment_proof' => 'required|image|max:2048',
        ]);

        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        $order->update([
            'payment_proof' => $path,
            'status' => 'process',
            'expires_at' => now()->addHour(), // 1 Hour timer for verification
        ]);

        return back()->with('success', 'Payment proof uploaded! Waiting for verification.');
    }
}
