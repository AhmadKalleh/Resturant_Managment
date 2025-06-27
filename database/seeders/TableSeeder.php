<?php

namespace Database\Seeders;

use App\Models\Table;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $locations = [
        // الدور الأرضي (الطابق الأول)
        ['en' => 'top-right corner - ground floor', 'ar' => 'الزاوية اليمنى العليا - الطابق الأرضي'],
        ['en' => 'top-left corner - ground floor', 'ar' => 'الزاوية اليسرى العليا - الطابق الأرضي'],
        ['en' => 'center of the room - ground floor', 'ar' => 'وسط القاعة - الطابق الأرضي'],
        ['en' => 'near the window - ground floor', 'ar' => 'بالقرب من النافذة - الطابق الأرضي'],
        ['en' => 'near the entrance - ground floor', 'ar' => 'بالقرب من المدخل - الطابق الأرضي'],
        ['en' => 'bottom-right corner - ground floor', 'ar' => 'الزاوية اليمنى السفلية - الطابق الأرضي'],
        ['en' => 'bottom-left corner - ground floor', 'ar' => 'الزاوية اليسرى السفلية - الطابق الأرضي'],
        ['en' => 'next to the bar - ground floor', 'ar' => 'بجوار البار - الطابق الأرضي'],
        ['en' => 'beside the stairs - ground floor', 'ar' => 'بجوار الدرج - الطابق الأرضي'],

        // الطابق العلوي (الثاني)
        ['en' => 'top-right corner - first floor', 'ar' => 'الزاوية اليمنى العليا - الطابق الأول'],
        ['en' => 'top-left corner - first floor', 'ar' => 'الزاوية اليسرى العليا - الطابق الأول'],
        ['en' => 'center of the room - first floor', 'ar' => 'وسط القاعة - الطابق الأول'],
        ['en' => 'near the window - first floor', 'ar' => 'بالقرب من النافذة - الطابق الأول'],
        ['en' => 'bottom-right corner - first floor', 'ar' => 'الزاوية اليمنى السفلية - الطابق الأول'],
        ['en' => 'bottom-left corner - first floor', 'ar' => 'الزاوية اليسرى السفلية - الطابق الأول'],
        ['en' => 'beside the balcony - first floor', 'ar' => 'بجانب الشرفة - الطابق الأول'],
        ['en' => 'next to the restroom - first floor', 'ar' => 'بجوار دورة المياه - الطابق الأول'],
    ];





        $counter=0;

        // 4 Tables for 2 people

        for($i = 0; $i < 4; $i++)
        {
            Table::query()->create([
            'seats' => 2,
            'location' => ['en' =>$locations[$counter]['en'],'ar'=>$locations[$counter++]['ar']],
            'price' => 50.00,
            'created_at' => now(),
            'updated_at' => now(),
            ])->image()->create([
                'path' =>'tables/2-Table.jpg'
            ]);
        }




        // 4 Tables for 4 people

        for($i = 0; $i < 4; $i++)
        {
            Table::query()->create([
            'seats' => 4,
            'location' => ['en' =>$locations[$counter]['en'],'ar'=>$locations[$counter++]['ar']],
            'price' => 100.00,
            'created_at' => now(),
            'updated_at' => now(),
            ])->image()->create([
                'path' =>'tables/4-Table.jpg'
            ]);
        }




        // 4 Tables for 5 people

        for($i = 0; $i < 4; $i++)
        {
            Table::query()->create([
            'seats' => 5,
            'location' => ['en' =>$locations[$counter]['en'],'ar'=>$locations[$counter++]['ar']],
            'price' => 120.00,
            'created_at' => now(),
            'updated_at' => now(),
            ])->image()->create([
                'path' =>'tables/5-Table.jpg'
            ]);
        }




        // 3 Tables for 6 people

        for($i = 0; $i < 3; $i++)
        {
            Table::query()->create([
            'seats' => 6,
            'location' => ['en' =>$locations[$counter]['en'],'ar'=>$locations[$counter++]['ar']],
            'price' => 140.00,
            'created_at' => now(),
            'updated_at' => now(),
            ])->image()->create([
                'path' =>'tables/6-Table.jpg'
            ]);
        }





        // 2 Tables for 13 people
        for($i = 0; $i < 2; $i++)
        {
            Table::query()->create([
            'seats' => 13,
            'location' => ['en' =>$locations[$counter]['en'],'ar'=>$locations[$counter++]['ar']],
            'price' => 250.00,
            'created_at' => now(),
            'updated_at' => now(),
            ])->image()->create([
                'path' =>'tables/13-Table.jpg'
            ]);
        }

    }
}
