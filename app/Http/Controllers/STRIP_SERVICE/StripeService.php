<?php

namespace App\Http\Controllers\STRIP_SERVICE;

use App\Models\User;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use Stripe\PaymentMethod;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class StripeService
{
    public $stripe;

    public function __construct()
    {
        $this->stripe =  new StripeClient(config('services.stripe.secret'));
    }

    public function pay()
    {


        $session = $this->stripe->checkout->sessions->create([
            'mode' => 'payment',
            'success_url' => 'http://example.com/success',
            'cancel_url' => 'http://example.com/cancel',
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Product Name',
                        ],
                        'unit_amount' => 2000, // 20.00 USD
                    ],
                    'quantity' => 10,
                ]
            ],
        ]);

        return redirect($session->url);
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
