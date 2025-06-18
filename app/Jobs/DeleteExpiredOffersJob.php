<?php

namespace App\Jobs;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
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

    public function handle()
    {
        $offer = Offer::withoutGlobalScopes()->find($this->offerId);

        if (!$offer) {
            return;
        }
        if ($offer->end_date >= now()->toDateString()) {
            return;
        }

        if ($offer->image) {
        $rawType = $offer->getRawOriginal('type');
        if ($rawType === 'special_day') {
            $offer->image()->delete();
            $offer->delete();
        }
        elseif ($rawType === 'normal_day') {
        if(Storage::disk('public')->exists($offer->image->path))
            {
                Storage::disk('public')->delete($offer->image->path);
            }
            $offer->image()->forceDelete();
            $offer->forceDelete();
        }
        }


    }

}
