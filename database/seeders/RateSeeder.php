<?php

namespace Database\Seeders;

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

        foreach ($products as $product)
        {
            $rate = round(mt_rand(10, 50) / 10, 1);

            $product->rating()->create([
                'rating' => $rate,
            ]);
        }
    }
}
