<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExtraProduct extends Model
{
    use HasFactory;

    protected $fillable = ['extra_id','product_id'];
    public function products() : BelongsToMany
    {
        return $this->belongsToMany(Product::class,'extra_products');
    }

    public function extras() : BelongsToMany
    {
        return $this->belongsToMany(Extra::class,'extra_products');
    }


    public function extra_products_cart_items():HasMany
    {
        return $this->hasMany(ExtraProductCartItem::class);
    }
}
