<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'chef_id' => 1,
                'name' => [
                    'en' => 'Top Ratings',
                    'ar' => 'الأعلى تقييما',
                ],
                'description' => [
                    'en' => 'All categories combined.',
                    'ar' => 'كل الفئات مجتمعة.',
                ],
            ],
            [
                'chef_id' => 1,
                'name' => [
                    'en' => 'Fast Food',
                    'ar' => 'مأكولات سريعة',
                ],
                'description' => [
                    'en' => 'Quick and tasty meals like burgers and fries.',
                    'ar' => 'وجبات سريعة ولذيذة مثل البرغر والبطاطا المقلية.',
                ],
            ],
            [
                'chef_id' => 2,
                'name' => [
                    'en' => 'Beverages',
                    'ar' => 'مشروبات',
                ],
                'description' => [
                    'en' => 'Refreshing drinks including juices and sodas.',
                    'ar' => 'مشروبات منعشة تشمل العصائر والمشروبات الغازية.',
                ],
            ],
            [
                'chef_id' => 3,
                'name' => [
                    'en' => 'Salads',
                    'ar' => 'سلطات',
                ],
                'description' => [
                    'en' => 'Healthy salads made from fresh ingredients.',
                    'ar' => 'سلطات صحية مصنوعة من مكونات طازجة.',
                ],
            ]
        ];//Fast_Food.jpg
        //Salads.jpg
        //Beverages.webp
        //All.png

        $category = Category::create($categories[0]);//categories/All.png
        $category->image()->create(['path' => 'categories/All.png']);

        $category2 = Category::create($categories[1]);
        $category2->image()->create(['path' => 'categories/Fast_Food.jpg']);

        $category3 = Category::create($categories[2]);
        $category3->image()->create(['path' => 'categories/Beverages.webp']);

        $category4 = Category::create($categories[3]);
        $category4->image()->create(['path' => 'categories/Salads.jpg']);

    }
}
