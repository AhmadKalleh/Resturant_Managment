<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasFactory,HasTranslations;

    public $translatable= ['name','description'];

    protected $fillable = ['cateogory_id','chef_id','name','description','price','calories','average_rating'];

    public function ingredients():BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class,'product__ingredients');
    }

    public function extras(): BelongsToMany
    {
        return $this->belongsToMany(Extra::class, 'extra_products');
    }

    public function offers(): BelongsToMany
    {
        return $this->belongsToMany(Offer::class, 'offer_products');
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
    public function category():BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function image()
    {
        return $this->morphOne(Image::class,'imageable');
    }

    public function ratings()
    {
        return $this->morphMany(Rating::class, 'rateable');
    }


    public function chef():BelongsTo
    {
        return $this->belongsTo(Chef::class);
    }

    public function getPriceTextAttribute():string
    {
        return rtrim(rtrim(number_format($this->price, 2, '.', ','), '0'), '.') . ' $';
    }

    public function getCaloriesTextAttribute():string
    {
        return $this->calories.' calories';
    }
}
