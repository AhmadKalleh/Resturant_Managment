<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStripeData extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'stripe_customer_id', 'default_payment_method_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
