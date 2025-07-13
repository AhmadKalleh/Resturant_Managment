<?php

namespace Database\Seeders;

use App\Models\Chef;
use App\Models\Complaints;
use App\Models\Customer;
use App\Models\Leave;
use App\Models\Reception;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إعداد بيانات تجريبية لكل نوع من المستخدمين (Chef و Receptionist)
        $chefs = Chef::all();
        $receptionists = Reception::all();

        // لكل Chef أضف بعض الإجازات
        foreach ($chefs as $chef) {
            Leave::create([
                'leaveable_id' => $chef->id,
                'leaveable_type' => Chef::class,
                'type' => 'annual',
                'start_date' => Carbon::now()->addDays(rand(1, 10)),
                'end_date' => Carbon::now()->addDays(rand(11, 20)),
                'reason' => 'Annual vacation for chef.',
                'status' => 'approved',
            ]);
        }

        // لكل Receptionist أضف بعض الإجازات
        foreach ($receptionists as $receptionist) {
            Leave::create([
                'leaveable_id' => $receptionist->id,
                'leaveable_type' => Reception::class,
                'type' => 'sick',
                'start_date' => Carbon::now()->subDays(rand(5, 10)),
                'end_date' => Carbon::now()->subDays(rand(1, 4)),
                'reason' => 'Medical leave.',
                'status' => 'pending',
            ]);
        }

        $customer = Customer::first(); // تأكد من وجود عميل على الأقل

        if ($customer) {
            Complaints::create([
                'customer_id' => $customer->id,
                'subject' => 'Late food delivery',
                'description' => 'I waited over an hour for my order to arrive.',
                'status' => 'pending',
            ]);

            Complaints::create([
                'customer_id' => $customer->id,
                'subject' => 'Wrong order received',
                'description' => 'I received the wrong items in my last order.',
                'status' => 'resolved',
                'response' => 'We apologize and will offer a replacement.',
                'responded_at' => now(),
            ]);

            Complaints::create([
                'customer_id' => $customer->id,
                'subject' => 'Unhygienic packaging',
                'description' => 'The food container was not sealed properly.',
                'status' => 'dismissed',
                'response' => 'We have reviewed and found no issue.',
                'responded_at' => now(),
            ]);
    }
    }
}
