<?php

namespace App\Http\Controllers;

use App\Models\BilliardTable;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $tables = BilliardTable::all()->keyBy('number');
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

        return view('booking.index', compact('tables', 'layoutMap'));
    }

    public function create($number)
    {
        $table = BilliardTable::where('number', $number)->firstOrFail();
        return view('booking.create', compact('table'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:billiard_tables,id',
            'start_time' => 'required|date',
            'duration' => 'required|integer|min:1',
        ]);

        $table = BilliardTable::findOrFail($request->table_id);

        $order = Order::create([
            'user_id' => auth()->id(),
            'billiard_table_id' => $table->id,
            'start_time' => $request->start_time,
            'end_time' => \Carbon\Carbon::parse($request->start_time)->addHours((int) $request->duration),
            'amount' => 50000 * (int) $request->duration,
            'status' => 'pending', // Initial status
        ]);

        Notification::create([
            'user_id' => auth()->id(),
            'title' => 'Booking Created',
            'message' => "Please upload payment proof for Table {$table->number}.",
        ]);

        return redirect()->route('dashboard')->with('success', 'Booking created! Please upload payment proof.');
    }

    public function uploadProof(Request $request, Order $order)
    {
        $request->validate([
            'payment_proof' => 'required|image|max:2048',
        ]);

        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        $order->update([
            'payment_proof' => $path,
            'status' => 'process', // Move to process after upload
        ]);

        return back()->with('success', 'Payment proof uploaded! Waiting for verification.');
    }
}
