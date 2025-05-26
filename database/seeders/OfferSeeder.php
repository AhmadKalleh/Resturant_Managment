<?php

namespace Database\Seeders;

use App\Jobs\DeleteExpiredOffersJob;
use App\Models\Category;
use App\Models\Offer;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


         // === Offer 1: Fast Food Combo ===

        $Fast_Food_Total = Product::query()->whereIn('id',[1,4,12,22])->sum('price');
        $Fast_Food_Discount_Value = $Fast_Food_Total * 0.2;
        $Fast_Food_Cambo = Offer::query()->create([
            'created_by' =>1,
            'title' => [
                'en'=>'Fast Food Combo',
                'ar'=>'وجبة كومبو للوجبات السريعة',
            ],
            'description' => [
                'en'=>'The meal includes a juicy cheeseburger with melted cheddar cheese, crispy golden French fries, a chilled Coca-Cola with visible ice cubes, and a fresh Caesar salad with croutons, lettuce',
                'ar'=>'الوجبة تتضمن برجر بالجبن الشهي مع جبن شيدر ذائب، وبطاطس مقلية ذهبية ومقرمشة، وعلبة كوكاكولا باردة مع مكعبات ثلج واضحة، وسلطة سيزر طازجة تحتوي على الخس وقطع الخبز المحمّص (كروتون).',
            ],
            'total_price' => $Fast_Food_Total,
            'price_after_discount' => $Fast_Food_Total - $Fast_Food_Discount_Value,
            'discount_value'       => '20%',
            'start_date'           => Carbon::today(),
            'end_date'             => Carbon::today()->addDays(5),
        ]);

        $Fast_Food_Cambo->products()->attach([1,4,12,22]);
        $Fast_Food_Cambo->image()->create([
            'path' =>'offers/Fast_Food_Cambo.jpg'
        ]);

        DeleteExpiredOffersJob::dispatch($Fast_Food_Cambo->id)->delay(now()->addDays(5));

         // === Offer 2: Refreshing Drinks ===

        $Refreshing_Drinks_Total = Product::query()->whereIn('id',[3,14,23])->sum('price');
        $Refreshing_Drinks_Discount_Value = $Refreshing_Drinks_Total * 0.15;
        $Crunch_Fresh = Offer::query()->create([
            'created_by' =>2,
            'title' => [
                'en'=>'Crunch & Fresh',
                'ar'=>'مُقرمش وطازج'
            ],
            'description' => [
                'en'=>'The meal includes a Fried Chicken with Mayonnaise, a chilled Lemon Mint with visible ice cubes, and a fresh Greek Salad',
                'ar'=>'الوجبة تتضمن دجاج مقلي مع المايونيز، وليمون بالنعناع بارد مع مكعبات ثلج واضحة، وسلطة يونانية طازجة.'
            ],
            'total_price' => $Refreshing_Drinks_Total,
            'price_after_discount' => $Refreshing_Drinks_Total - $Refreshing_Drinks_Discount_Value,
            'discount_value'       => '15%',
            'start_date'           => Carbon::today(),
            'end_date'             => Carbon::today()->addDays(3),
        ]);

        $Crunch_Fresh->products()->attach([3,14,23]);
        $Crunch_Fresh->image()->create([
            'path' =>'offers/Crunch.jpg'
        ]);

        DeleteExpiredOffersJob::dispatch($Crunch_Fresh->id)->delay(now()->addDays(3));

        // === Offer 3: Healthy Salads ===

        $Healthy_Salads_Total = Product::query()->whereIn('id',[2,13,27])->sum('price');
        $Healthy_Salads_Discount_Value = $Healthy_Salads_Total * 0.25;
        $Veggie_Boost = Offer::query()->create([
            'created_by' =>3,
            'title' => [
                'en'=>'Veggie & Boost',
                'ar'=>'مُقرمش وطازج'
            ],
            'description' => [
                'en'=>'The meal includes a grilled veggie burger with lettuce and tomato, aa glass of fresh orange juice with a straw and ice cubes, and a bowl of avocado salad with corn and leafy greens',
                'ar'=>'الوجبة تتضمن برجر نباتي مشوي مع الخس والطماطم، وكأسًا من عصير البرتقال الطازج مع قشة ومكعبات ثلج، ووعاء من سلطة الأفوكادو مع الذرة والخضروات الورقية.'
            ],
            'total_price' => $Healthy_Salads_Total,
            'price_after_discount' => $Healthy_Salads_Total - $Healthy_Salads_Discount_Value,
            'discount_value'       => '25%',
            'start_date'           => Carbon::today(),
            'end_date'             => Carbon::today()->addDays(7),
        ]);

        $Veggie_Boost->products()->attach([2,13,27]);
        $Veggie_Boost->image()->create([
            'path' =>'offers/Veggie_Boost.jpg'
        ]);

        DeleteExpiredOffersJob::dispatch($Veggie_Boost->id)->delay(now()->addDays(7));

    }
}
