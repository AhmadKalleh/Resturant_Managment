<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id','reservation_id','status','total_amount','guest_name','guest_mobile','is_guest'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function reservation() : BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function payment():HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function carts() : HasMany
    {
        return $this->hasMany(Cart::class);
    }
}
