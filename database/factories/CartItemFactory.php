<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CartItem>
 */
class CartItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(5, 10);
        $hasOffer = fake()->boolean();
        $price = fake()->randomFloat(2, 5, 100);

        return [
            'cart_id' => Cart::inRandomOrder()->first()?->id ?? Cart::factory(),
            'product_id' => $hasOffer ? null : Product::inRandomOrder()->first()?->id,
            'offer_id'   => null,
            'price_at_order' => $price,
            'total_price' => $price * $quantity,
            'quantity' => $quantity,
            'is_selected_for_checkout' => true,
            'is_pre_order' => false,
            'prepare_at' => null,
        ];
    }
}
