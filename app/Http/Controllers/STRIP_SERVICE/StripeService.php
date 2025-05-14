<?php

namespace App\Http\Controllers\STRIP_SERVICE;

use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCharge($amount, $currency, $source, $description)
    {
        try {
            return Charge::create([
                'amount' => $amount * 100,
                'currency' => $currency,
                'source' => $source,  // ✅ استخدم source بدلاً من payment_method
                'description' => $description,
            ]);
        } catch (ApiErrorException $e) {
            return ['error' => $e->getMessage()];
        }
    }

}
