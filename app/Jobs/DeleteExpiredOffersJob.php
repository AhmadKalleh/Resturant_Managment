<?php

namespace App\Jobs;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteExpiredOffersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    public function __construct(public $id)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Offer::query()
            ->where('id', $this->id)
            ->where('end_date', '<=', now())
            ->delete();

    }
}
