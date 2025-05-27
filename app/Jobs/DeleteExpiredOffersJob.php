<?php

namespace App\Jobs;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Storage;

class DeleteExpiredOffersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */


    protected int $offerId;

    public function __construct(int $offerId)
    {
        $this->offerId = $offerId;
    }

    public function handle(): void
    {
        $offer = Offer::withoutGlobalScopes()->find($this->offerId);

        if (!$offer) {
            return;
        }
        if ($offer->end_date >= now()->toDateString()) {
            return;
        }

        if ($offer->image) {
            Storage::disk('public')->delete($offer->image->path);
            $offer->image()->delete();
        }

        $offer->delete();
    }

}
