<?php

namespace App\Http\Controllers\Order;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    public function index_pre_orders():array
    {
        $lang = Auth::user()->preferred_language;

        $pre_orders = Order::query()
        ->with(['carts', 'carts.cart_items'])
        ->where('customer_id', Auth::user()->customer->id)
        ->whereHas('carts.cart_items', function ($query) {
            $query->where('is_selected_for_checkout', true)
                ->where('is_pre_order', true)
                ->where('prepare_at', '>', now()->addHours(3));
        })
        ->orderByDesc('created_at') // لا يمكن ترتيب الطلبات مباشرة بناءً على cart_items.prepare_at
        ->get()
        ->map(function($pre_order){

            $pre_order->carts

        });


    }
}
