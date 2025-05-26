<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['cart_id','product_id','price_at_order','total_price','quantity'];


    protected static function boot()
    {
        parent::boot();

        static::updating(function ($cartItem)
        {
            $cartItem->total_price = $cartItem->price_at_order * $cartItem->quantity;
        });
    }


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function cart() : BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }
    public function extra_products():BelongsToMany
    {
        return $this->belongsToMany(ExtraProduct::class,'extra_product_cart_items', );
    }

    public function getTotalPriceTextAttribute()
    {
        return number_format($this->total_price, 0, ',', ',') . ' $';
    }

    public function scopeTotalPriceForCart($query, $cartId)
    {
        return $query->where('cart_id', $cartId)->sum('total_price');
    }

}
