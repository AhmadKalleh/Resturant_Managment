<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Table extends Model
{
    use HasFactory,HasTranslations;

    protected $fillable = ['image_id','seats','location','status'];

    public $translatable= ['location'];

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function getSeatsTextAttribute(): string
    {
        return $this->seats.' seats';
    }

}
