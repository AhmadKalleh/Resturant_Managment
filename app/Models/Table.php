<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Table extends Model
{
    use HasFactory,HasTranslations;

    protected $fillable = ['image_id','seats','location','status','price'];

    public $translatable= ['location'];

    public function image()
    {
        return $this->morphOne(Image::class,'imageable');
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function getSeatsTextAttribute(): string
    {
        return $this->seats.' seats';
    }

    public function getPriceTextAttribute():string
    {
        return rtrim(rtrim(number_format($this->price, 2, '.', ','), '0'), '.') . ' $';
    }

}
