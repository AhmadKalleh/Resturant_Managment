<?php

namespace App\Jobs;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

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

        $now = now()->addHours(3);

        $expiredReservations = Reservation::where('is_checked_in', false)
            ->where('is_canceled', false)
            ->where(function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                    $q->where('is_extended_delay', false)
                    ->where('reservation_start_time', '<=', $now->copy()->subMinutes(30));
                })->orWhere(function ($q) use ($now) {
                    $q->where('is_extended_delay', true)
                    ->where('reservation_start_time', '<=', $now->copy()->subMinutes(45));
                });
            })
            ->get();

        dump(['reservation count : '=> $expiredReservations->count()]);

        foreach ($expiredReservations as $reservation) {
            try {

                $reservation->update([
                    'is_canceled' => true,
                    'canceled_by' => 'system'
                ]);

                dump(['Updated reservation ID' => $reservation->id]);

                // متابعة مع الزبون
                $customer = $reservation->customer;
                if ($customer) {
                    $customer->increment('no_show_count');

                    $customer->behaviorLogs()->create([
                        'type' => json_encode(['auto_canceled']),
                        'action_date' => now(),
                    ]);

                    if ($customer->no_show_count % 3 == 0) {
                        $weeks = $customer->no_show_count / 3;
                        $customer->blocked_until = now()->addWeeks($weeks);
                        $customer->block_reservation = true;
                        $customer->save();
                    }
                }
            } catch (\Throwable $e) {
                dump([
                    'Exception occurred for reservation ID' => $reservation->id,
                    'Message' => $e->getMessage()
                ]);
                \Log::error("AutoCancelReservation Error: " . $e->getMessage());
            }
        }

        }
    }


