<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $pre_order = Order::query()
        ->create([
            'customer_id' => 4,
            'reservation_id' => 3,
            'total_amount' => 180
        ]);

        $res = $pre_order->reservation;

        $cart = Cart::query()->create([
            'order_id' => $pre_order->id,
            'customer_id' => 4,
            'is_checked_out' => true
        ]);

        $cartItem1 = $cart->cart_items()->create([
            'product_id' => 1,
            'price_at_order' => 25,
            'quantity' => 2,
            'total_price' => 25 * 2,
            'is_selected_for_checkout' => true,
            'is_pre_order' => true,
            'prepare_at' => Carbon::parse($res->reservation_start_time)->addMinutes(10)
        ]);
        $cartItem1->extra_products()->attach([1, 2]);

        $cartItem2 = $cart->cart_items()->create([
            'product_id' => 2,
            'price_at_order' => 28,
            'quantity' => 2,
            'total_price' => 28 * 2,
            'is_selected_for_checkout' => true,
            'is_pre_order' => true,
            'prepare_at' => Carbon::parse($res->reservation_start_time)->addMinutes(10)
        ]);
        $cartItem2->extra_products()->attach([6, 7]);

        $cartItem3 = $cart->cart_items()->create([
            'product_id' => 3,
            'price_at_order' => 30,
            'quantity' => 2,
            'total_price' => 30 * 2,
            'is_selected_for_checkout' => true,
            'is_pre_order' => true,
            'prepare_at' => Carbon::parse($res->reservation_start_time)->addMinutes(10)
        ]);

        $cartItem3->extra_products()->attach([11, 12]);


        $cart = Cart::query()->create([
            'order_id' => null,
            'customer_id' => 4,
            'is_checked_out' => false
        ]);

        $cartItem4 = $cart->cart_items()->create([
            'product_id' => 4,
            'price_at_order' => 10,
            'quantity' => 2,
            'total_price' => 10 * 2,
            'is_selected_for_checkout' => false,
            'is_pre_order' => false,
        ]);

        $cartItem4->extra_products()->attach([16, 17]);


    }
}
