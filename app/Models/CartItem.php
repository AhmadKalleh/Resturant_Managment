<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['cart_id','product_id','price_at_order','total_price','quantity'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function cart() : BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }
    public function extra_products_cart_items():HasMany
    {
        return $this->hasMany(ExtraProductCartItem::class);
    }
}
