<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call(ImageSeeder::class);
        $this->call(RolesPermissionsSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(IngredientsSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(RateSeeder::class);
        $this->call(ExtraSeeder::class);
        $this->call(OfferSeeder::class);
        $this->call(TableSeeder::class);
        $this->call(ResSeeder::class);
        $this->call(CartSeeder::class);
    }
}
