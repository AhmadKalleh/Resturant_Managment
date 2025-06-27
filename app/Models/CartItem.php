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

    protected $fillable = ['cart_id','product_id','price_at_order','total_price','quantity','offer_id'];


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

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function getTotalPriceTextAttribute()
    {
        return rtrim(rtrim(number_format($this->total_price, 2, '.', ','), '0'), '.') . ' $';
    }

    public function scopeTotalPriceForCart($query, $cartId)
    {
        $cartItems = $query->with('extra_products')->where('cart_id', $cartId)->where('is_selected_for_checkout',false)->get();

        $total = 0;

        foreach ($cartItems as $item)
        {
            $itemTotal = $item->total_price;

            $extraTotal = $item->extra_products->sum(function ($extraProduct) {
                return $extraProduct->extra->price ?? 0;
                });

            $total += $itemTotal + $extraTotal;
        }

        return rtrim(rtrim(number_format($total, 2, '.', ','), '0'), '.');
    }

}
