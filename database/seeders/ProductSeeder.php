<?php

namespace Database\Seeders;


use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //grilled veggie burger
        $Fast_Food = [
            ['name_en' => 'Cheeseburger', 'name_ar' => 'تشيز برجر', 'desc_en' => 'Grilled beef patty with cheese', 'desc_ar' => 'برجر لحم بقري مشوي مع جبنة', 'price' => 25, 'calories' => 510],
            ['name_en' => 'Grilledveggieburger','name_ar' => 'برجر نباتي مشوي','desc_en' => 'Grilled veggie patty with fresh toppings','desc_ar' => 'برجر نباتي مشوي مع إضافات طازجة','price' => 28,'calories' => 290,],
            ['name_en' => 'Fried Chicken', 'name_ar' => 'دجاج مقلي', 'desc_en' => 'Crispy fried chicken pieces', 'desc_ar' => 'قطع دجاج مقرمشة مقلية', 'price' => 30, 'calories' => 420],
            ['name_en' => 'French Fries', 'name_ar' => 'بطاطا مقلية', 'desc_en' => 'Golden crispy fries', 'desc_ar' => 'بطاطا مقلية ذهبية مقرمشة', 'price' => 10, 'calories' => 230],
            ['name_en' => 'Hot Dog', 'name_ar' => 'هوت دوغ', 'desc_en' => 'Classic beef hot dog', 'desc_ar' => 'هوت دوغ بلحم بقري كلاسيكي', 'price' => 18, 'calories' => 260],
            ['name_en' => 'Chicken Nuggets', 'name_ar' => 'ناجتس الدجاج', 'desc_en' => 'Bite-sized chicken nuggets', 'desc_ar' => 'قطع صغيرة من ناجتس الدجاج', 'price' => 15, 'calories' => 70],
            ['name_en' => 'Onion Rings', 'name_ar' => 'حلقات بصل', 'desc_en' => 'Fried onion rings', 'desc_ar' => 'حلقات بصل مقلية', 'price' => 12, 'calories' => 230],
            ['name_en' => 'Pizza Slice', 'name_ar' => 'شريحة بيتزا', 'desc_en' => 'Cheesy pepperoni pizza', 'desc_ar' => 'بيتزا بجبنة وبيبروني', 'price' => 20, 'calories' => 330],
            ['name_en' => 'Chicken Wrap', 'name_ar' => 'راب دجاج', 'desc_en' => 'Grilled chicken wrap', 'desc_ar' => 'راب دجاج مشوي', 'price' => 22, 'calories' => 310],
            ['name_en' => 'Beef Taco', 'name_ar' => 'تاكو لحم', 'desc_en' => 'Spicy beef taco', 'desc_ar' => 'تاكو بلحم حار', 'price' => 17, 'calories' => 220],
            ['name_en' => 'Mozzarella Sticks', 'name_ar' => 'أصابع موتزاريلا', 'desc_en' => 'Fried cheese sticks', 'desc_ar' => 'أصابع جبنة موتزاريلا مقلية', 'price' => 14, 'calories' => 105],
        ];

            $product = Product::query()->create([
                'category_id' => 2,
                    'chef_id' => 1,
                    'name' => [
                        'en' => $Fast_Food[0]['name_en'],
                        'ar' => $Fast_Food[0]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Fast_Food[0]['desc_en'],
                        'ar' => $Fast_Food[0]['desc_ar'],
                    ],
                    'price' => $Fast_Food[0]['price'],
                    'calories' => $Fast_Food[0]['calories'],
            ]);
            $product->image()->create(['path' =>'products/Cheeseburger.png']);

            $product->ingredients()->attach([1,2,3,4,5]);

            $product1 = Product::query()->create([
                'category_id' => 2,
                    'chef_id' => 1,
                    'name' => [
                        'en' => $Fast_Food[1]['name_en'],
                        'ar' => $Fast_Food[1]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Fast_Food[1]['desc_en'],
                        'ar' => $Fast_Food[1]['desc_ar'],
                    ],
                    'price' => $Fast_Food[1]['price'],
                    'calories' => $Fast_Food[1]['calories'],
            ]);
            $product1->image()->create(['path' =>'products/Grilledveggieburger.png']);

            $product1->ingredients()->attach([6,7,8]);

            $product2 = Product::query()->create([
                'category_id' => 2,
                    'chef_id' => 1,
                    'name' => [
                        'en' => $Fast_Food[2]['name_en'],
                        'ar' => $Fast_Food[2]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Fast_Food[2]['desc_en'],
                        'ar' => $Fast_Food[2]['desc_ar'],
                    ],
                    'price' => $Fast_Food[2]['price'],
                    'calories' => $Fast_Food[2]['calories'],
            ]);
            $product2->image()->create(['path' =>'products/Fried_Chicken.png']);

            $product2->ingredients()->attach([9,10]);

            $product3 = Product::query()->create([
                'category_id' => 2,
                    'chef_id' => 1,
                    'name' => [
                        'en' => $Fast_Food[3]['name_en'],
                        'ar' => $Fast_Food[3]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Fast_Food[3]['desc_en'],
                        'ar' => $Fast_Food[3]['desc_ar'],
                    ],
                    'price' => $Fast_Food[3]['price'],
                    'calories' => $Fast_Food[3]['calories'],
            ]);
            $product3->image()->create(['path' =>'products/French_Fries.png']);

            $product3->ingredients()->attach([11,12]);

            $product4 = Product::query()->create([
                'category_id' => 2,
                    'chef_id' => 1,
                    'name' => [
                        'en' => $Fast_Food[4]['name_en'],
                        'ar' => $Fast_Food[4]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Fast_Food[4]['desc_en'],
                        'ar' => $Fast_Food[4]['desc_ar'],
                    ],
                    'price' => $Fast_Food[4]['price'],
                    'calories' => $Fast_Food[4]['calories'],
            ]);
            $product4->image()->create(['path' =>'products/Hot_Dog.png']);

            $product4->ingredients()->attach([13,14]);

            $product5 = Product::query()->create([
                'category_id' => 2,
                    'chef_id' => 1,
                    'name' => [
                        'en' => $Fast_Food[5]['name_en'],
                        'ar' => $Fast_Food[5]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Fast_Food[5]['desc_en'],
                        'ar' => $Fast_Food[5]['desc_ar'],
                    ],
                    'price' => $Fast_Food[5]['price'],
                    'calories' => $Fast_Food[5]['calories'],
            ]);
            $product5->image()->create(['path' =>'products/Chicken_Nuggets.png']);

            $product5->ingredients()->attach([15,16]);

            $product6 = Product::query()->create([
                'category_id' => 2,
                    'chef_id' => 1,
                    'name' => [
                        'en' => $Fast_Food[6]['name_en'],
                        'ar' => $Fast_Food[6]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Fast_Food[6]['desc_en'],
                        'ar' => $Fast_Food[6]['desc_ar'],
                    ],
                    'price' => $Fast_Food[6]['price'],
                    'calories' => $Fast_Food[6]['calories'],
            ]);
            $product6->image()->create(['path' =>'products/Onion_Rings.png']);

            $product6->ingredients()->attach([17,18]);

            $product7 = Product::query()->create([
                'category_id' => 2,
                    'chef_id' => 1,
                    'name' => [
                        'en' => $Fast_Food[7]['name_en'],
                        'ar' => $Fast_Food[7]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Fast_Food[7]['desc_en'],
                        'ar' => $Fast_Food[7]['desc_ar'],
                    ],
                    'price' => $Fast_Food[7]['price'],
                    'calories' => $Fast_Food[7]['calories'],
            ]);
            $product7->image()->create(['path' =>'products/Pizza_Slice.png']);

            $product7->ingredients()->attach([19,20,21,22]);

            $product9 = Product::query()->create([
                'category_id' => 2,
                    'chef_id' => 1,
                    'name' => [
                        'en' => $Fast_Food[9]['name_en'],
                        'ar' => $Fast_Food[9]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Fast_Food[9]['desc_en'],
                        'ar' => $Fast_Food[9]['desc_ar'],
                    ],
                    'price' => $Fast_Food[9]['price'],
                    'calories' => $Fast_Food[9]['calories'],
            ]);
            $product9->image()->create(['path' =>'products/Beef_Taco.png']);

            $product9->ingredients()->attach([26,27,28]);

            $product10 = Product::query()->create([
                'category_id' => 2,
                    'chef_id' => 1,
                    'name' => [
                        'en' => $Fast_Food[10]['name_en'],
                        'ar' => $Fast_Food[10]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Fast_Food[10]['desc_en'],
                        'ar' => $Fast_Food[10]['desc_ar'],
                    ],
                    'price' => $Fast_Food[10]['price'],
                    'calories' => $Fast_Food[10]['calories'],
            ]);
            $product10->image()->create(['path' =>'products/Mozzarella_Sticks.png']);
            $product10->ingredients()->attach([29,30]);





        $Drinks = [
            ['name_en' => 'Coca Cola', 'name_ar' => 'كوكاكولا', 'desc_en' => 'Chilled cola drink', 'desc_ar' => 'مشروب كولا بارد', 'price' => 8, 'calories' => 39],
            ['name_en' => 'Orange Juice', 'name_ar' => 'عصير برتقال', 'desc_en' => 'Freshly squeezed orange juice', 'desc_ar' => 'عصير برتقال طازج', 'price' => 10, 'calories' => 115],
            ['name_en' => 'Lemon Mint', 'name_ar' => 'ليمون ونعنع', 'desc_en' => 'Refreshing lemon with mint', 'desc_ar' => 'عصير ليمون منعش مع نعناع', 'price' => 12, 'calories' => 44],
            ['name_en' => 'Iced Coffee', 'name_ar' => 'قهوة مثلجة', 'desc_en' => 'Cold brew coffee', 'desc_ar' => 'قهوة باردة مثلجة', 'price' => 15, 'calories' => 86],
            ['name_en' => 'Green Tea', 'name_ar' => 'شاي أخضر', 'desc_en' => 'Healthy green tea', 'desc_ar' => 'شاي أخضر صحي', 'price' => 9, 'calories' => 0],
            ['name_en' => 'Milkshake', 'name_ar' => 'ميلك شيك', 'desc_en' => 'Creamy vanilla milkshake', 'desc_ar' => 'ميلك شيك فانيليا كريمي', 'price' => 18, 'calories' => 300],
            ['name_en' => 'Water', 'name_ar' => 'ماء', 'desc_en' => 'Mineral water', 'desc_ar' => 'مياه معدنية', 'price' => 5, 'calories' => 0],
            ['name_en' => 'Strawberry Juice', 'name_ar' => 'عصير فراولة', 'desc_en' => 'Fresh strawberry juice', 'desc_ar' => 'عصير فراولة طازج', 'price' => 11, 'calories' => 47],
            ['name_en' => 'Peach Iced Tea', 'name_ar' => 'شاي مثلج بالخوخ', 'desc_en' => 'Chilled peach tea', 'desc_ar' => 'شاي بارد بالخوخ', 'price' => 13, 'calories' => 55],
            ['name_en' => 'Energy Drink', 'name_ar' => 'مشروب طاقة', 'desc_en' => 'High caffeine energy drink', 'desc_ar' => 'مشروب طاقة غني بالكافيين', 'price' => 16, 'calories' => 39],
        ];



            $product11 = Product::query()->create([
                'category_id' => 3,
                    'chef_id' => 2,
                    'name' => [
                        'en' => $Drinks[0]['name_en'],
                        'ar' => $Drinks[0]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Drinks[0]['desc_en'],
                        'ar' => $Drinks[0]['desc_ar'],
                    ],
                    'price' => $Drinks[0]['price'],
                    'calories' => $Drinks[0]['calories'],
            ]);
            $product11->image()->create(['path' =>'products/Coca_Cola.png']);

            $product11->ingredients()->attach([31,32,40]);

            $product12 = Product::query()->create([
                'category_id' => 3,
                    'chef_id' => 2,
                    'name' => [
                        'en' => $Drinks[1]['name_en'],
                        'ar' => $Drinks[1]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Drinks[1]['desc_en'],
                        'ar' => $Drinks[1]['desc_ar'],
                    ],
                    'price' => $Drinks[1]['price'],
                    'calories' => $Drinks[1]['calories'],
            ]);
            $product12->image()->create(['path' =>'products/Orange_Juice.png']);

            $product12->ingredients()->attach([33,34,35]);

            $product13 = Product::query()->create([
                'category_id' => 3,
                    'chef_id' => 2,
                    'name' => [
                        'en' => $Drinks[2]['name_en'],
                        'ar' => $Drinks[2]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Drinks[2]['desc_en'],
                        'ar' => $Drinks[2]['desc_ar'],
                    ],
                    'price' => $Drinks[2]['price'],
                    'calories' => $Drinks[2]['calories'],
            ]);
            $product13->image()->create(['path' =>'products/Lemon_Mint.png']);

            $product13->ingredients()->attach([32,34,35]);

            $product14 = Product::query()->create([
                'category_id' => 3,
                    'chef_id' => 2,
                    'name' => [
                        'en' => $Drinks[3]['name_en'],
                        'ar' => $Drinks[3]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Drinks[3]['desc_en'],
                        'ar' => $Drinks[3]['desc_ar'],
                    ],
                    'price' => $Drinks[3]['price'],
                    'calories' => $Drinks[3]['calories'],
            ]);
            $product14->image()->create(['path' =>'products/Iced_Coffee.png']);

            $product14->ingredients()->attach([32,36,37]);

            $product15 = Product::query()->create([
                'category_id' => 3,
                    'chef_id' => 2,
                    'name' => [
                        'en' => $Drinks[4]['name_en'],
                        'ar' => $Drinks[4]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Drinks[4]['desc_en'],
                        'ar' => $Drinks[4]['desc_ar'],
                    ],
                    'price' => $Drinks[4]['price'],
                    'calories' => $Drinks[4]['calories'],
            ]);
            $product15->image()->create(['path' =>'products/Green_Tea.png']);

            $product15->ingredients()->attach([42,43]);

            $product16 = Product::query()->create([
                'category_id' => 3,
                    'chef_id' => 2,
                    'name' => [
                        'en' => $Drinks[5]['name_en'],
                        'ar' => $Drinks[5]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Drinks[5]['desc_en'],
                        'ar' => $Drinks[5]['desc_ar'],
                    ],
                    'price' => $Drinks[5]['price'],
                    'calories' => $Drinks[5]['calories'],
            ]);
            $product16->image()->create(['path' =>'products/Milkshake.png']);

            $product16->ingredients()->attach([32,37,41]);

            $product17 = Product::query()->create([
                'category_id' => 3,
                    'chef_id' => 2,
                    'name' => [
                        'en' => $Drinks[6]['name_en'],
                        'ar' => $Drinks[6]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Drinks[6]['desc_en'],
                        'ar' => $Drinks[6]['desc_ar'],
                    ],
                    'price' => $Drinks[6]['price'],
                    'calories' => $Drinks[6]['calories'],
            ]);
            $product17->image()->create(['path' =>'products/Water.png']);

            $product17->ingredients()->attach([43]);



            $product19 = Product::query()->create([
                'category_id' => 3,
                    'chef_id' => 2,
                    'name' => [
                        'en' => $Drinks[8]['name_en'],
                        'ar' => $Drinks[8]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Drinks[8]['desc_en'],
                        'ar' => $Drinks[8]['desc_ar'],
                    ],
                    'price' => $Drinks[8]['price'],
                    'calories' => $Drinks[8]['calories'],
            ]);
            $product19->image()->create(['path' =>'products/Peach_Iced_Tea.png']);

            $product19->ingredients()->attach([32,39,42]);

            $product20 = Product::query()->create([
                'category_id' => 3,
                    'chef_id' => 2,
                    'name' => [
                        'en' => $Drinks[9]['name_en'],
                        'ar' => $Drinks[9]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Drinks[9]['desc_en'],
                        'ar' => $Drinks[9]['desc_ar'],
                    ],
                    'price' => $Drinks[9]['price'],
                    'calories' => $Drinks[9]['calories'],
            ]);
            $product20->image()->create(['path' =>'products/Energy_Drink.png']);

            $product20->ingredients()->attach([31,32,40]);



        $Salads = [
            ['name_en' => 'Caesar Salad', 'name_ar' => 'سلطة سيزر', 'desc_en' => 'Lettuce, parmesan & croutons', 'desc_ar' => 'خس مع جبنة وبرش توست', 'price' => 20, 'calories' => 61],
            ['name_en' => 'Greek Salad', 'name_ar' => 'سلطة يونانية', 'desc_en' => 'Tomatoes, olives & feta', 'desc_ar' => 'طماطم وزيتون وجبنة فيتا', 'price' => 18, 'calories' => 137],
            ['name_en' => 'Tabbouleh', 'name_ar' => 'تبولة', 'desc_en' => 'Parsley and bulgur salad', 'desc_ar' => 'سلطة بقدونس وبرغل', 'price' => 14, 'calories' => 297],
            ['name_en' => 'Fattoush', 'name_ar' => 'فتوش', 'desc_en' => 'Mixed veggies with toasted bread', 'desc_ar' => 'خضار مشكلة مع خبز محمص', 'price' => 15, 'calories' => 184],
            ['name_en' => 'Chicken Salad', 'name_ar' => 'سلطة دجاج', 'desc_en' => 'Grilled chicken with greens', 'desc_ar' => 'دجاج مشوي مع خضار طازجة', 'price' => 22, 'calories' => 162],
            ['name_en' => 'Avocado Salad', 'name_ar' => 'سلطة أفوكادو', 'desc_en' => 'Avocado with lemon and greens', 'desc_ar' => 'أفوكادو مع ليمون وخضار', 'price' => 24, 'calories' => 84],
            ['name_en' => 'Quinoa Salad', 'name_ar' => 'سلطة الكينوا', 'desc_en' => 'Healthy quinoa mix', 'desc_ar' => 'خليط صحي من الكينوا', 'price' => 21, 'calories' => 150],
            ['name_en' => 'Coleslaw', 'name_ar' => 'سلطة كول سلو', 'desc_en' => 'Creamy cabbage salad', 'desc_ar' => 'سلطة ملفوف بكريمة', 'price' => 13, 'calories' => 64 ],
            ['name_en' => 'Pasta Salad', 'name_ar' => 'سلطة معكرونة', 'desc_en' => 'Cold pasta with veggies', 'desc_ar' => 'معكرونة باردة مع خضار', 'price' => 17, 'calories' => 180],
            ['name_en' => 'Tuna Salad', 'name_ar' => 'سلطة تونة', 'desc_en' => 'Tuna with mixed greens', 'desc_ar' => 'تونة مع خضار مشكلة', 'price' => 20, 'calories' => 162],
        ];

        $product21 = Product::query()->create([
                'category_id' => 4,
                    'chef_id' => 3,
                    'name' => [
                        'en' => $Salads[0]['name_en'],
                        'ar' => $Salads[0]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Salads[0]['desc_en'],
                        'ar' => $Salads[0]['desc_ar'],
                    ],
                    'price' => $Salads[0]['price'],
                    'calories' => $Salads[0]['calories'],
            ]);
        $product21->image()->create(['path' =>'products/Caesar_Salad.png']);

        $product21->ingredients()->attach([44,45,46]);

        $product22 = Product::query()->create([
                'category_id' => 4,
                    'chef_id' => 3,
                    'name' => [
                        'en' => $Salads[1]['name_en'],
                        'ar' => $Salads[1]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Salads[1]['desc_en'],
                        'ar' => $Salads[1]['desc_ar'],
                    ],
                    'price' => $Salads[1]['price'],
                    'calories' => $Salads[1]['calories'],
            ]);
        $product22->image()->create(['path' =>'products/Greek_Salad.png']);

        $product22->ingredients()->attach([47,48,49]);

        $product23 = Product::query()->create([
                'category_id' => 4,
                    'chef_id' => 3,
                    'name' => [
                        'en' => $Salads[2]['name_en'],
                        'ar' => $Salads[2]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Salads[2]['desc_en'],
                        'ar' => $Salads[2]['desc_ar'],
                    ],
                    'price' => $Salads[2]['price'],
                    'calories' => $Salads[2]['calories'],
            ]);
        $product23->image()->create(['path' =>'products/Tabbouleh.png']);

        $product23->ingredients()->attach([44,47,50,51,53,56]);

        $product24 = Product::query()->create([
                'category_id' => 4,
                    'chef_id' => 3,
                    'name' => [
                        'en' => $Salads[3]['name_en'],
                        'ar' => $Salads[3]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Salads[3]['desc_en'],
                        'ar' => $Salads[3]['desc_ar'],
                    ],
                    'price' => $Salads[3]['price'],
                    'calories' => $Salads[3]['calories'],
            ]);
        $product24->image()->create(['path' =>'products/Fattoush.png']);

        $product24->ingredients()->attach([46,52,53]);

        $product25 = Product::query()->create([
                'category_id' => 4,
                    'chef_id' => 3,
                    'name' => [
                        'en' => $Salads[4]['name_en'],
                        'ar' => $Salads[4]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Salads[4]['desc_en'],
                        'ar' => $Salads[4]['desc_ar'],
                    ],
                    'price' => $Salads[4]['price'],
                    'calories' => $Salads[4]['calories'],
            ]);
        $product25->image()->create(['path' =>'products/Chicken_Salad.png']);

        $product25->ingredients()->attach([52,58]);

        $product26 = Product::query()->create([
                'category_id' => 4,
                    'chef_id' => 3,
                    'name' => [
                        'en' => $Salads[5]['name_en'],
                        'ar' => $Salads[5]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Salads[5]['desc_en'],
                        'ar' => $Salads[5]['desc_ar'],
                    ],
                    'price' => $Salads[5]['price'],
                    'calories' => $Salads[5]['calories'],
            ]);
        $product26->image()->create(['path' =>'products/Avocado_Salad.png']);

        $product26->ingredients()->attach([34,52,54]);

        $product27 = Product::query()->create([
                'category_id' => 4,
                    'chef_id' => 3,
                    'name' => [
                        'en' => $Salads[6]['name_en'],
                        'ar' => $Salads[6]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Salads[6]['desc_en'],
                        'ar' => $Salads[6]['desc_ar'],
                    ],
                    'price' => $Salads[6]['price'],
                    'calories' => $Salads[6]['calories'],
            ]);
        $product27->image()->create(['path' =>'products/Quinoa_Salad.png']);

        $product27->ingredients()->attach([52,55]);

        $product28 = Product::query()->create([
                'category_id' => 4,
                    'chef_id' => 3,
                    'name' => [
                        'en' => $Salads[7]['name_en'],
                        'ar' => $Salads[7]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Salads[7]['desc_en'],
                        'ar' => $Salads[7]['desc_ar'],
                    ],
                    'price' => $Salads[7]['price'],
                    'calories' => $Salads[7]['calories'],
            ]);
        $product28->image()->create(['path' =>'products/Coleslaw.png']);

        $product28->ingredients()->attach([37,56]);

        $product29 = Product::query()->create([
                'category_id' => 4,
                    'chef_id' => 3,
                    'name' => [
                        'en' => $Salads[8]['name_en'],
                        'ar' => $Salads[8]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Salads[8]['desc_en'],
                        'ar' => $Salads[8]['desc_ar'],
                    ],
                    'price' => $Salads[8]['price'],
                    'calories' => $Salads[8]['calories'],
            ]);
        $product29->image()->create(['path' =>'products/Pasta.png']);

        $product29->ingredients()->attach([52,57]);

        $product30 = Product::query()->create([
                'category_id' => 4,
                    'chef_id' => 3,
                    'name' => [
                        'en' => $Salads[9]['name_en'],
                        'ar' => $Salads[9]['name_ar'],
                    ],
                    'description' => [
                        'en' => $Salads[9]['desc_en'],
                        'ar' => $Salads[9]['desc_ar'],
                    ],
                    'price' => $Salads[9]['price'],
                    'calories' => $Salads[9]['calories'],
            ]);
        $product30->image()->create(['path' =>'products/Tuna_Salad.png']);

        $product30->ingredients()->attach([52,58]);




        }
    }

