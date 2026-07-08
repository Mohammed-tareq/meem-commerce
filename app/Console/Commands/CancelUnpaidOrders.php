<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Marvel\Database\Models\Order;
use Marvel\Database\Models\Transaction;
use Marvel\Events\PaymentFailed;

class CancelUnpaidOrders extends Command
{
    protected $signature = 'orders:cancel-unpaid';
    protected $description = 'Cancel unpaid pending orders past their timeout period';

    public function handle(): int
    {
        $cutoff = now()->subHours(config('payment.order_timeout_hours', 72));

        $orders = Order::query()
            ->where('status', 'pending')
            ->where('created_at', '<=', $cutoff)
            ->cursor();

        $cancelledCount = 0;

        foreach ($orders as $order) {
            DB::transaction(function () use ($order, &$cancelledCount) {
                $order->update(['status' => 'cancelled']);

                $order->transactions()
                    ->where('status', 'pending')
                    ->update(['status' => 'failed']);

                try {
                    event(new PaymentFailed($order));
                } catch (\Throwable $e) {
                    report($e);
                }

                $cancelledCount++;
            });
        }

        $this->info("Cancelled {$cancelledCount} unpaid order(s).");

        return self::SUCCESS;
    }
}
