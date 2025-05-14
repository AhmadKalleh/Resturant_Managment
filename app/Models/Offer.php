<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Offer extends Model
{
    use HasFactory;


    protected $fillable = ['chef_id','title','description','total_price','price_after_discount','discount_value','start_date','end_date'];

    public function chef():BelongsTo
    {
        return $this->belongsTo(Chef::class);
    }

    public function offer_products() : HasMany
    {
        return $this->hasMany(OfferProduct::class);
    }
}
