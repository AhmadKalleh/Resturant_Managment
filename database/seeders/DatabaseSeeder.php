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



        $this->call(ImageSeeder::class);
        $this->call(RolesPermissionsSeeder::class);
        $this->call(TableSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(IngredientsSeeder::class);
        $this->call(ProductSeeder::class);
        //$this->call(ImportBigData::class);
        $this->call(RateSeeder::class);
        $this->call(ExtraSeeder::class);
        $this->call(OfferSeeder::class);
        $this->call(LeaveSeeder::class);

        // $this->call(ResSeeder::class);
        // $this->call(CartSeeder::class);
    }
}
