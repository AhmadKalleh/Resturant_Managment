<?php

namespace App\Jobs;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoCancelExpiredReservations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $now = now()->addHours(3)->subMinutes(31);

        dump(['now' => $now->toDateTimeString()]);

        $expiredReservations = Reservation::where('is_checked_in', false)
            ->where('is_canceled', false)
            ->where('reservation_start_time', '<=', $now)
            ->get();

        dump(['reservation count : '=> $expiredReservations->count()]);

        foreach ($expiredReservations as $reservation) {


            $reservation->update([
                'is_canceled' => true,
                'canceled_by' => 'system'
            ]);

            // سجل في log سلوك الزبون (اختياري)
            $customer = $reservation->customer;
            if ($customer) {
                $customer->increment('no_show_count');

                $customer->behaviorLogs()->create([
                    'type' => json_encode(['auto_canceled']),
                    'action_date' => now(),
                ]);

                // حظر الزبون إذا لزم الأمر
                if ($customer->no_show_count % 3 == 0) {
                    $weeks = $customer->no_show_count / 3;
                    $customer->blocked_until = now()->addWeeks($weeks);
                    $customer->block_reservation = true;
                    $customer->save();
                }
            }
        }

    }
}
