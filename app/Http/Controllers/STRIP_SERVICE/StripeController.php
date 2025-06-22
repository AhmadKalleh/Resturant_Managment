<?php

namespace App\Http\Controllers\STRIP_SERVICE;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class StripeController extends Controller
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
            'payment_method_types' => ['card'],
            'success_url' => 'http://example.com/success',
            'cancel_url' => 'http://example.com/cancel',
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Food Order',
                        ],
                        'unit_amount' => 4000,
                    ],
                    'quantity' => 10,
                ],
            ]
        ]);

        Payment::query()->create([
            'order_id' => 1,
            'amount' => 4000 * 10, // المجموع الكلي
            'payment_method' => 'visa',
        ]);

        return redirect($session->url);
    }


}
