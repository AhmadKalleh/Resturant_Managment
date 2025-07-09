<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IngredientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = [
            // Cheeseburger
            ['name' => ['en'=>'Beef Patty','ar'=>'قطعة لحم بقري'], 'calories'=>250],
            ['name' => ['en'=>'Cheddar Cheese','ar'=>'جبنة شيدر'], 'calories'=>113],
            ['name' => ['en'=>'Lettuce','ar'=>'خس'], 'calories'=>5],
            ['name' => ['en'=>'Tomato','ar'=>'طماطم'], 'calories'=>22],
            ['name' => ['en'=>'Burger Bun','ar'=>'خبز البرجر'], 'calories'=>120],

            // Grilled Veggie Burger
            ['name' => ['en'=>'Veggie Patty','ar'=>'قطعة نباتية'], 'calories'=>150],
            ['name' => ['en'=>'Whole Wheat Bun','ar'=>'خبز قمح كامل'], 'calories'=>110],
            ['name' => ['en'=>'Fresh Toppings','ar'=>'إضافات طازجة'], 'calories'=>30],

            // Fried Chicken
            ['name' => ['en'=>'Chicken Pieces','ar'=>'قطع دجاج'], 'calories'=>300],
            ['name' => ['en'=>'Frying Oil','ar'=>'زيت قلي'], 'calories'=>120],

            // French Fries
            ['name' => ['en'=>'Potatoes','ar'=>'بطاطا'], 'calories'=>230],
            ['name' => ['en'=>'Salt','ar'=>'ملح'], 'calories'=>0],

            // Hot Dog
            ['name' => ['en'=>'Hot Dog Sausage','ar'=>'نقانق هوت دوغ'], 'calories'=>150],
            ['name' => ['en'=>'Hot Dog Bun','ar'=>'خبز هوت دوغ'], 'calories'=>110],

            // Chicken Nuggets
            ['name' => ['en'=>'Chicken Nugget','ar'=>'قطع ناجتس'], 'calories'=>45],
            ['name' => ['en'=>'Breading','ar'=>'بقسماط تغطية'], 'calories'=>25],

            // Onion Rings
            ['name' => ['en'=>'Onion Rings','ar'=>'بصل مقلي'], 'calories'=>150],
            ['name' => ['en'=>'Batter','ar'=>'طعينة قلي'], 'calories'=>80],

            // Pizza Slice
            ['name' => ['en'=>'Pizza Dough','ar'=>'عجينة البيتزا'], 'calories'=>150],
            ['name' => ['en'=>'Pepperoni','ar'=>'بيبروني'], 'calories'=>80],
            ['name' => ['en'=>'Mozzarella','ar'=>'موتزاريلا'], 'calories'=>70],
            ['name' => ['en'=>'Tomato Sauce','ar'=>'صلصة طماطم'], 'calories'=>30],

            // Chicken Wrap
            ['name' => ['en'=>'Tortilla','ar'=>'تورتيلا'], 'calories'=>120],
            ['name' => ['en'=>'Grilled Chicken','ar'=>'دجاج مشوي'], 'calories'=>150],
            ['name' => ['en'=>'Wrap Veggies','ar'=>'خضار راب'], 'calories'=>40],

            // Beef Taco
            ['name' => ['en'=>'Taco Shell','ar'=>'قشرة تاكو'], 'calories'=>70],
            ['name' => ['en'=>'Spicy Beef','ar'=>'لحم لاذع'], 'calories'=>120],
            ['name' => ['en'=>'Taco Veggies','ar'=>'خضار تاكو'], 'calories'=>30],

            // Mozzarella Sticks
            ['name' => ['en'=>'Mozzarella Stick','ar'=>'أصبع موتزاريلا'], 'calories'=>80],
            ['name' => ['en'=>'Breading','ar'=>'بقسماط تغطية'], 'calories'=>25],

            // Drinks
            ['name' => ['en'=>'Carbonated Water','ar'=>'ماء مكربن'], 'calories'=>0],//31
            ['name' => ['en'=>'Sugar Syrup','ar'=>'شراب السكر'], 'calories'=>39], // for cola 32
            ['name' => ['en'=>'Orange Juice','ar'=>'عصير برتقال'], 'calories'=>110],//33
            ['name' => ['en'=>'Lemon','ar'=>'ليمون'], 'calories'=>4],//34
            ['name' => ['en'=>'Mint Leaves','ar'=>'نعناع'], 'calories'=>1],//35
            ['name' => ['en'=>'Cold Brew Coffee','ar'=>'قهوة باردة'], 'calories'=>5],//36
            ['name' => ['en'=>'Milk','ar'=>'حليب'], 'calories'=>42],//37
            ['name' => ['en'=>'Strawberry','ar'=>'فراولة'], 'calories'=>4],//38
            ['name' => ['en'=>'Peach','ar'=>'خوخ'], 'calories'=>7],//39
            ['name' => ['en'=>'Caffeine','ar'=>'كافيين'], 'calories'=>0],//40
            ['name' => ['en'=>'Vanilla Ice Cream','ar'=>'آيس كريم فانيليا'], 'calories'=>219],//41
            ['name' => ['en'=>'Green Tea Leaves','ar'=>'أوراق شاي أخضر'], 'calories'=>0], //42
            ['name' => ['en'=>'Water','ar'=>'ماء'], 'calories'=>0],//43

            // Salads
            ['name' => ['en'=>'Lettuce','ar'=>'خس'], 'calories'=>5],//44
            ['name' => ['en'=>'Parmesan','ar'=>'جبن بارميزان'], 'calories'=>21],//45
            ['name' => ['en'=>'Croutons','ar'=>'خبز محمص'], 'calories'=>35],//46
            ['name' => ['en'=>'Tomatoes','ar'=>'طماطم'], 'calories'=>22],//47
            ['name' => ['en'=>'Olives','ar'=>'زيتون'], 'calories'=>40],//48
            ['name' => ['en'=>'Feta Cheese','ar'=>'جبنة فيتا'], 'calories'=>75],//49
            ['name' => ['en'=>'Parsley','ar'=>'بقدونس'], 'calories'=>1],//50
            ['name' => ['en'=>'Bulgur','ar'=>'برغل'], 'calories'=>150],//51
            ['name' => ['en'=>'Mixed Veggies','ar'=>'خضار مشكلة'], 'calories'=>30],//52
            ['name' => ['en'=>'Olive Oil','ar'=>'زيت زيتون'], 'calories'=>119],//53
            ['name' => ['en'=>'Avocado','ar'=>'أفوكادو'], 'calories'=>50],//54
            ['name' => ['en'=>'Quinoa','ar'=>'كينوا'], 'calories'=>120],//55
            ['name' => ['en'=>'Cabbage','ar'=>'ملفوف'], 'calories'=>22],//56
            ['name' => ['en'=>'Pasta','ar'=>'معكرونة'], 'calories'=>150],//57
            ['name' => ['en'=>'Tuna','ar'=>'تونة'], 'calories'=>132],//58
        ];

        foreach ($ingredients as $item) {
            DB::table('ingredients')->insert([
                'name' => json_encode($item['name']),
                'calories' => $item['calories'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
