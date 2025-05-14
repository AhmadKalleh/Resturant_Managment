<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ExtraProductCartItem extends Model
{
    use HasFactory;

    protected $fillable = ['cart_item_id','extra_product_cart_item_id'];
    public function  cart_items() : BelongsToMany
    {
        return $this->belongsToMany(CartItem::class,'extra_product_cart_items');
    }

    public function extra_products() : BelongsToMany
    {
        return $this->belongsToMany(ExtraProduct::class,'extra_product_cart_items');
    }
}
