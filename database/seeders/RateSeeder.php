<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Nette\Utils\Random;

class RateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $customers = Customer::limit(10)->get();

        foreach ($products as $product) {
            foreach ($customers as $customer) {
                $rate = mt_rand(1, 5); // قيمة عشوائية من 1.0 إلى 5.0

                $product->ratings()->create([
                    'customer_id' => $customer->id,
                    'rating' => $rate,
                ]);
            }

            // حساب المتوسط وتحديثه
            $average = $product->ratings()->avg('rating');
            $product->update(['average_rating' => round($average, 2)]);
        }
    }
}
