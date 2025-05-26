<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExtraProduct extends Model
{
    use HasFactory;

    protected $fillable = ['extra_id','product_id'];

    public function cart_items():BelongsToMany
    {
        return $this->BelongsToMany(CartItem::class,'extra_product_cart_items',);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function extra(): BelongsTo
    {
        return $this->belongsTo(Extra::class);
    }
}
