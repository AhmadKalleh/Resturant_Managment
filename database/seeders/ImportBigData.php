<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use PhpParser\Node\Expr\Cast;

class ImportBigData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        // USERS :
        \App\Models\User::factory(200)->create();



        // CUSTOMERS :
        $users = \App\Models\User::where('id', '>=', 12)->get();

        foreach ($users as $user) {
            // 2. لكل مستخدم، أنشئ سجل واحد في customers مربوط به
            \App\Models\Customer::factory()->create([
                'user_id' => $user->id,
            ]);
        }


        // RESERVATIONS :
        $customers = Customer::all();
        $tableIds = Table::pluck('id')->toArray();

        foreach($customers as $customer)
        {
            $randomTableIds = Arr::random($tableIds, 2);
            Reservation::factory()->create([
                'customer_id' => $customer->id,
                'table_id' => $randomTableIds[0],
            ]);

            Reservation::factory()->create([
                'customer_id' => $customer->id,
                'table_id' => $randomTableIds[1],
            ]);

        }



        // ORDERS :
        $reservations = Reservation::all()->toArray();

        $i=0;

        foreach ($customers as $customer) {
            for ($j = 0; $j < rand(1,2); $j++) {
                if (!isset($reservations[$i])) {
                    break; // تجنب الخطأ في حالة انتهاء الحجوزات
                }

                $res = $reservations[$i++];
                $start = Carbon::parse($res['reservation_start_time']);
                $end = Carbon::parse($res['reservation_end_time']);

                // تأكد أن وقت البداية أصغر من النهاية
                if ($start->lt($end)) {
                    $randomCreatedAt = Carbon::createFromTimestamp(
                        rand($start->timestamp, $end->timestamp)
                    );
                } else {
                    $randomCreatedAt = $start; // fallback في حال الخطأ
                }

                Order::factory()->create([
                    'customer_id' => $customer->id,
                    'reservation_id' => $res['id'],
                    'created_at' => $randomCreatedAt,
                    'updated_at' => $randomCreatedAt, // optional
                ]);
            }
        }





        $orders = Order::with('reservation.table')->get();

        // CARTS
        foreach ($orders as $order)
        {
            Cart::factory()->create([
                'order_id' => $order->id,
                'customer_id' => $order->customer_id
            ]);
        }


        // CART_ITEMS
        $carts = Cart::with(['order','cart_items.product'])->get();
        $productIds = Product::pluck('id')->toArray();

        foreach ($carts as $cart) {
            // إذا لم يكن هناك طلب مرتبط، تجاهل هذه السلة
            if (!$cart->order) continue;

            $randomProductIds = Arr::random($productIds, 3);

            $sum = 0;
            foreach ($randomProductIds as $productId) {
                $product = Product::find($productId);
                $quantity = fake()->numberBetween(5, 10);

                $cart_item = CartItem::factory()->create([
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                    'price_at_order' => $product->price,
                    'quantity' => $quantity,
                    'total_price' => $product->price * $quantity,
                ]);

                $sum += $cart_item->total_price;
            }

            // تحديث المجموع داخل الطلب المرتبط
            $cart->order->update([
                'total_amount' => $sum
            ]);
        }


        // PAYMENTS
        $orders = Order::with('reservation.table')->get(); // eager load

        foreach ($orders as $order) {
            $reservation = $order->reservation;

            if ($reservation && $reservation->table) {
                Payment::factory()->create([
                    'order_id'   => $order->id,
                    'amount'     => $order->total_amount + $reservation->table->price,
                    'created_at' => $order->created_at,
                ]);
            }
        }

    }
}
