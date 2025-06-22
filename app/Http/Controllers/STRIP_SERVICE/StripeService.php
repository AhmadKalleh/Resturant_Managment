<?php

namespace App\Http\Controllers\STRIP_SERVICE;

use App\Models\User;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use Stripe\PaymentMethod;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createStripeCustomer(User $user)
    {
        try {
            $stripeCustomer = Customer::create([
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'phone' => $user->mobile,
                'metadata' => [
                    'laravel_user_id' => $user->id,
                ],
            ]);


            return $stripeCustomer;
        } catch (\Exception $e) {
            report($e);
            return ['error' => $e->getMessage()];
        }
    }


    public function attachPaymentMethodToCustomer($paymentMethodId, $customerId)
    {
        return PaymentMethod::retrieve($paymentMethodId)->attach(['customer' => $customerId]);
    }

    public function chargeCustomer($amount, $currency, $customerId, $source)
    {
        try {
        $paymentIntent = Charge::create([
            'amount' => $amount * 100, // بالـ cents
            'currency' => $currency,
            'customer' => $customerId,
            'source' => $source,
            'off_session' => true,
            'confirm' => true,
        ]);

            return $paymentIntent;
        } catch (\Exception $e) {
            report($e);
            throw $e;
        }
    }
}
