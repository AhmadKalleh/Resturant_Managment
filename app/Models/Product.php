<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasFactory,HasTranslations;

    public $translatable= ['name','description'];

    protected $fillable = ['cateogory_id','image_id','rating_id','chef_id','name','description','price','calories'];

    public function offer_productes() : HasMany
    {
        return $this->hasMany(OfferProduct::class);
    }

    public function extra_products() : HasMany
    {
        return $this->hasMany(ExtraProduct::class);
    }

    public function cart_Itemes() : HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function favorites() : HasMany
    {
        return $this->hasMany(Favorite::class);
    }
    public function catogery():BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function image():HasOne
    {
        return $this->hasOne(Image::class);
    }

    public function rating(): HasOne
    {
        return $this->hasOne(Rating::class);
    }

    public function chef():BelongsTo
    {
        return $this->belongsTo(Chef::class);
    }

    public function getPriceTextAttribute():string
    {
        $price = number_format($this->amount,3,'.','') .'SYP';
        return $price;
    }
}
