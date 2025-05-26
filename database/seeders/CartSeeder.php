<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cart = Cart::query()->create([
            'order_id' => null,
            'customer_id' => 4,
            'is_checked_out' => false
        ]);

        $cart->cart_items()->createMany([
            [
                'product_id' => 1,
                'price_at_order' => 25,
                'quantity' => 100,
                'total_price' => 25 * 100
            ],
            [
                'product_id' => 2,
                'price_at_order' => 28,
                'quantity' => 5,
                'total_price' => 28 * 5
            ],
            [
                'product_id' => 3,
                'price_at_order' => 30,
                'quantity' => 2,
                'total_price' => 30 * 2
            ]
        ]);

    }
}
