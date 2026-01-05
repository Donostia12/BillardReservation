<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Notification;

class CleanupExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel pending orders that have expired.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredOrders = Order::whereIn('status', ['pending', 'process'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->with(['billiardTable', 'user'])
            ->get();

        $count = $expiredOrders->count();

        if ($count > 0) {
            foreach ($expiredOrders as $order) {
                $order->update(['status' => 'cancelled']);

                // Create notification for the user
                Notification::create([
                    'user_id' => $order->user_id,
                    'title' => 'Booking Expired',
                    'message' => "Your booking for Table {$order->billiardTable->number} was cancelled due to timeout.",
                ]);
            }
            $this->info("Cancelled {$count} expired orders.");
        } else {
            $this->info('No expired orders found.');
        }
    }
}
