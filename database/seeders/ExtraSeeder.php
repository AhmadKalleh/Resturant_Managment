<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Extra;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExtraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fastFoodExtras = [
            ['en' => 'Extra Cheese', 'ar' => 'جبنة إضافية', 'price' => 2.5, 'calories' => 110],
            ['en' => 'Bacon', 'ar' => 'لحم مقدد', 'price' => 3.0, 'calories' => 150],
            ['en' => 'Spicy Sauce', 'ar' => 'صلصة حارة', 'price' => 1.5, 'calories' => 50],
            ['en' => 'Ketchup', 'ar' => 'كاتشب', 'price' => 0.5, 'calories' => 20],
            ['en' => 'Mayonnaise', 'ar' => 'مايونيز', 'price' => 0.5, 'calories' => 90],
            ['en' => 'Pickles', 'ar' => 'مخلل', 'price' => 1.0, 'calories' => 5],
            ['en' => 'Grilled Onions', 'ar' => 'بصل مشوي', 'price' => 1.5, 'calories' => 40],
            ['en' => 'Double Patty', 'ar' => 'قطعتين لحم', 'price' => 5.0, 'calories' => 320],
            ['en' => 'Mushrooms', 'ar' => 'فطر', 'price' => 2.0, 'calories' => 15],
            ['en' => 'Lettuce', 'ar' => 'خس', 'price' => 1.0, 'calories' => 5],
        ];


        // ✅ إضافات مشروبات
        $drinkExtras = [
        ['en' => 'Ice Cubes', 'ar' => 'مكعبات ثلج', 'price' => 0.0, 'calories' => 0],
        ['en' => 'Lemon Slice', 'ar' => 'شريحة ليمون', 'price' => 0.5, 'calories' => 2],
        ['en' => 'Mint Leaves', 'ar' => 'أوراق نعناع', 'price' => 1.0, 'calories' => 1],
        ['en' => 'Extra Sugar', 'ar' => 'سكر إضافي', 'price' => 0.5, 'calories' => 30],
        ['en' => 'No Sugar', 'ar' => 'بدون سكر', 'price' => 0.0, 'calories' => 0],
        ['en' => 'Caramel Shot', 'ar' => 'نكهة كراميل', 'price' => 2.0, 'calories' => 60],
        ['en' => 'Vanilla Shot', 'ar' => 'نكهة فانيليا', 'price' => 2.0, 'calories' => 50],
        ['en' => 'Whipped Cream', 'ar' => 'كريمة مخفوقة', 'price' => 1.5, 'calories' => 100],
        ['en' => 'Chocolate Syrup', 'ar' => 'شراب شوكولاتة', 'price' => 1.5, 'calories' => 70],
        ['en' => 'Espresso Shot', 'ar' => 'جرعة إسبريسو', 'price' => 3.0, 'calories' => 5],
    ];


        // ✅ إضافات سلطات
        $saladExtras = [
        ['en' => 'Feta Cheese', 'ar' => 'جبنة فيتا', 'price' => 2.0, 'calories' => 75],//21
        ['en' => 'Olives', 'ar' => 'زيتون', 'price' => 1.5, 'calories' => 45],//22
        ['en' => 'Croutons', 'ar' => 'خبز محمص', 'price' => 1.0, 'calories' => 80],//23
        ['en' => 'Boiled Egg', 'ar' => 'بيض مسلوق', 'price' => 2.0, 'calories' => 70],//24
        ['en' => 'Tuna', 'ar' => 'تونة', 'price' => 3.0, 'calories' => 90],//25
        ['en' => 'Chicken Strips', 'ar' => 'شرائح دجاج', 'price' => 3.5, 'calories' => 120],//26
        ['en' => 'Avocado Slices', 'ar' => 'شرائح أفوكادو', 'price' => 3.0, 'calories' => 80],//27
        ['en' => 'Corn', 'ar' => 'ذرة', 'price' => 1.0, 'calories' => 60],//28
        ['en' => 'Balsamic Dressing', 'ar' => 'صلصة بلسميك', 'price' => 1.5, 'calories' => 40],//29
        ['en' => 'Yogurt Sauce', 'ar' => 'صلصة الزبادي', 'price' => 1.0, 'calories' => 35],//30
    ];


        // ✅ إنشاء السجلات
        foreach ($fastFoodExtras as $extra) {
            Extra::create([
                'chef_id' => 1,
                'name' => [
                    'en' => $extra['en'],
                    'ar' => $extra['ar'],
                ],
                'price' => $extra['price'],
                'calories' =>$extra['calories']
            ]);
        }

        foreach ($drinkExtras as $extra) {
            Extra::create([
                'chef_id' => 2,
                'name' => [
                    'en' => $extra['en'],
                    'ar' => $extra['ar'],
                ],
                'price' => $extra['price'],
                'calories' =>$extra['calories']
            ]);
        }

        foreach ($saladExtras as $extra) {
            Extra::create([
                'chef_id' => 3,
                'name' => [
                    'en' => $extra['en'],
                    'ar' => $extra['ar'],
                ],
                'price' => $extra['price'],
                'calories' =>$extra['calories']
            ]);
        }

        $category = Category::with('products')->where('id', 2)->first();
        $fastFoddProducts = $category->products;

        $fastFoddProducts[0]->extras()->attach([1, 2, 3, 4, 5]);
        $fastFoddProducts[1]->extras()->attach([1, 3, 5, 6, 8]);
        $fastFoddProducts[2]->extras()->attach([2, 3, 4, 9, 10]);
        $fastFoddProducts[3]->extras()->attach([5, 7, 9, 10]);
        $fastFoddProducts[4]->extras()->attach([1, 2, 3]);
        $fastFoddProducts[5]->extras()->attach([4, 8, 9, 10]);
        $fastFoddProducts[6]->extras()->attach([1, 3, 5, 7, 9]);
        $fastFoddProducts[7]->extras()->attach([5, 8, 9, 10]);
        $fastFoddProducts[8]->extras()->attach([1, 2, 3, 4]);


        $category = Category::with('products')->where('id', 3)->first();
        $drinks = $category->products;


        $drinks[0]->extras()->attach([11, 15]);                  // Coca Cola
        $drinks[1]->extras()->attach([11, 12, 15]);              // Orange Juice
        $drinks[2]->extras()->attach([11, 12, 13, 15]);          // Lemon Mint
        $drinks[3]->extras()->attach([11, 16, 17, 18, 20]);      // Iced Coffee
        $drinks[4]->extras()->attach([11, 13, 15]);              // Green Tea
        $drinks[5]->extras()->attach([17, 18, 19]);              // Milkshake
        $drinks[6]->extras()->attach([11]);                      // Water
        // $drinks[7]->extras()->attach([11, 14, 13]);              // Strawberry Juice
        $drinks[7]->extras()->attach([11, 16, 17, 15]);          // Peach Iced Tea
        $drinks[8]->extras()->attach([11, 15]);                  // Energy Drink

        $category = Category::with('products')->where('id', 4)->first();
        $salads = $category->products;


        $salads[0]->extras()->attach([23, 24, 26, 29]);
        $salads[1]->extras()->attach([21, 22, 27, 29]);       // Greek Salad
        $salads[2]->extras()->attach([24, 28, 29, 30]);       // Tabbouleh
        $salads[3]->extras()->attach([23, 28, 29, 30]);       // Fattoush
        $salads[4]->extras()->attach([26, 27, 29, 30]);       // Chicken Salad
        $salads[5]->extras()->attach([22, 27, 29, 30]);       // Avocado Salad
        $salads[6]->extras()->attach([21, 27, 28, 29, 30]);   // Quinoa Salad
        $salads[7]->extras()->attach([24, 28, 29, 30]);       // Coleslaw
        $salads[8]->extras()->attach([21, 22, 23, 28]);       // Pasta Salad
        $salads[9]->extras()->attach([21, 22, 24, 28]);       // Tuna Salad
    }
}
