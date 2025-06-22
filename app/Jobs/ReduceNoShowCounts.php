<?php

namespace App\Jobs;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReduceNoShowCounts implements ShouldQueue
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
        dump(['reduce work ......']);
        $thresholdDate = now()->subWeek(); // شهرين

        $customers = Customer::where('no_show_count', '>', 0)->get();

        foreach ($customers as $customer) {
            $lastNoShow = $customer->behaviorLogs()
                ->orderBy('action_date', 'desc')
                ->first();

            if (!$lastNoShow || $lastNoShow->action_date < $thresholdDate) {
                $customer->decrement('no_show_count');

                if ($customer->no_show_count <= 0)
                {
                    $customer->update([
                        'no_show_count' => 0,
                        'block_reservation' => false,
                        'blocked_until' => null,
                    ]);
                }
            }
        }
    }
}
