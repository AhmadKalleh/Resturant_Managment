<?php

namespace Database\Seeders;

use App\Http\Controllers\STRIP_SERVICE\StripeService;
use App\Models\Chef;
use App\Models\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Stripe\Customer;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $resturant_manager_role = Role::create(['name'=>'resturant_manager','guard_name' => 'web']);
        $chef_role = Role::create(['name' => 'chef','guard_name' => 'web']);
        $reception_role = Role::create(['name' => 'reception','guard_name' => 'web']);
        $customer_role = Role::create(['name' => 'customer','guard_name' => 'web']);
        $visitor_role = Role::create(['name' => 'visitor','guard_name' => 'web']);




        $resturant_manager_permissions =
        [
            'create-table','update-table','delete-table','show-table','index-table','update-theme','update-lan',
            'delete-offer','show-offer','index-offer','index-rating','update-rating','send_message',
            'create-chef','update-chef','delete-chef','show-chef','index-chef','index-chat','index-chat_message','send-message',
            'create-customer','update-customer','delete-customer','show-customer','index-customer','show_own_extra_for_product',
            'create-reception','update-reception','delete-reception','index-reception','show-reception',
            'delete-reservations','show-reservations','index-reservations','create-offer','update-offer',
            'create-products','update-products','delete-products','show-products','index-products',
            'create-categories','update-categories','delete-categories','show-categories','index-categories',
            'show-order','index-order','behavior-monitoring','view-statistics','manage-profile','check-in-reservation',
            'index-extra','create-extra','update-extra','delete-extra','show-extra','create-order','transfer-ownership',
            'create-reservation','approve-reservation','reject-reservation','confirm-arrival','manage-profile',
            'mark-order-complete','delete-reservation','show-reservation','index-reservation','filter','delete_extra_product',
            'index-favorite','create-favorite','delete-favorite','create-rating','update-rating','create-cart','update-cart','index-cart',
            'show-info','change-mobile','update-password','update-image-profile','delete-account','show_extra_product_details','store_extra_product','ChargeMywallet','show_my_wallet'
        ];
        foreach($resturant_manager_permissions as $permission)
        {
            Permission::findOrCreate($permission,'web');
        }

        // Assign permissions to roles




        // delete old permissions and keep those inside the $permissions array
        $resturant_manager_role->syncPermissions($resturant_manager_permissions);

        $chef_permissions = [
            'create-products','update-products','delete-products','show-products','index-products','update-theme','update-lan',
            'create-categories','update-categories','delete-categories','show-categories','index-categories','store_extra_product',
            'create-offer','update-offer','delete-offer','show-offer','index-offer','index-extra','delete_extra_product',
            'create-extra','update-extra','delete-extra','show-extra','manage-profile','show_extra_product_details',
            'index-order','mark-order-complete','show-info','change-mobile','update-password','update-image-profile','delete-account'
        ];

        $chef_role->syncPermissions($chef_permissions);




        // add permissions  on top of old ones
        $reception_role->givePermissionTo(
            [
                'create-table','update-table','delete-table','show-table','index-table','update-theme','update-lan',
                'approve-reservation','reject-reservation','confirm-arrival','manage-profile','check-in-reservation',
                'create-reservation','delete-reservation','show-reservation','index-reservation',
                'show-info','change-mobile','update-password','update-image-profile','delete-account',

            ]);

        $customer_role->givePermissionTo(
        [
            'show-categories','index-categories','show-products','index-products','filter',
            'show-offer','index-offer','index-favorite','create-favorite','delete-favorite',
            'create-rating','update-rating','create-cart','update-cart','index-cart','show_own_extra_for_product',
            'create-reservation','delete-reservation','show-reservation','index-reservation','ChargeMywallet','show_my_wallet',
            'create-order','show-order','index-order','manage-profile','show-table','index-table',
            'show-info','change-mobile','update-password','update-image-profile','delete-account',
            'index-chat','index-chat_message','send-message','update-theme','update-lan',
        ]);


        // Visitor Permissions:

$visitor_permissions =
            [
                'show-categories',
                'index-categories',
                'show-products',
                'index-products',
                'filter',
                'show-offer',
                'index-offer',
                'index-rating',
            ];

        $visitor_role->givePermissionTo($visitor_permissions);


        ////////////////////////////////////////////////////////


        // ================ Resturant Manager ================


        $resturant_manager = User::query()->create([
            'first_name' => 'Ahmad',
            'last_name' =>'Kalleh',
            'email' => 'ahmadhkalleh@gmail.com',
            'password' => Hash::make('a72xd2004'),
            'mobile' => '+963995884773',
            'gendor' =>'male',
            'date_of_birth' =>'2004-03-31',
            'preferred_language' =>'ar',
            'preferred_theme' =>'light',
        ]);



        $resturant_manager->assignRole($resturant_manager_role);
        $permissions = $resturant_manager_role->permissions()->pluck('name')->toArray();
        $resturant_manager->givePermissionTo($permissions);


        // ================ Chefs ================


        $chef = User::query()->create([
            'first_name' => 'Jad',
            'last_name'=>'Nahkla',
            'email' => 'jadnahkla@example.com',
            'password' => Hash::make('j72xd2004'),
            'mobile' => '+963912764899',
            'gendor' =>'male',
            'date_of_birth' =>'2004-08-15',
            'preferred_language' =>'ar',
            'preferred_theme' =>'dark',
        ]);


        $chef->chef()->create([
            'speciality' => ['en' => ['Italian Cuisine', 'Seafood'],'ar' => ['المطبخ الإيطالي', 'المأكولات البحرية'],],
            'years_of_experience' => 10,
            'bio' => 'Chef Mario has over a decade of experience specializing in authentic Italian dishes and fresh seafood. He studied in Naples and worked in several 5-star restaurants across Europe.',
            'certificates' => json_encode(['Le Cordon Bleu Diploma', 'Italian Culinary Institute Certificate'])
        ]);



        $chef2 = User::query()->create([
            'first_name' => 'Omar',
            'last_name'=>'Sobh',
            'email' => 'omarsobh@example.com',
            'password' => Hash::make('o72xd2004'),
            'mobile' => '+963976223467',
            'gendor' =>'male',
            'date_of_birth' =>'2004-07-04',
            'preferred_language' =>'ar',
            'preferred_theme' =>'dark',
        ]);
        $chef2->chef()->create([
            'speciality' => ['en' => ['Japanese Cuisine', 'Sushi'],'ar' => ['المطبخ الياباني', 'السوشي'],],
            'years_of_experience' => 7,
            'bio' => 'Chef Aiko is an expert in traditional Japanese cuisine with a strong focus on sushi and sashimi. Trained in Tokyo, she brings precision and artistry to every dish.',
            'certificates' => json_encode(['Tokyo Sushi Academy Certification', 'Japanese Culinary Arts Certificate'])
        ]);;



        $chef3 = User::query()->create([
            'first_name' => 'Alyamama',
            'last_name'=>'Kadi',
            'email' => 'alyamamakadi@example.com',
            'password' => Hash::make('y72xd2004'),
            'mobile' => '+963988761123',
            'gendor' =>'female',
            'date_of_birth' =>'2004-12-20',
            'preferred_language' =>'en',
            'preferred_theme' =>'light',
        ]);
        $chef3->chef()->create([
            'speciality' => ['en' => ['French Pastry', 'Desserts'],'ar' => ['المعجنات الفرنسية', 'الحلويات'],],
            'years_of_experience' => 5,
            'bio' => 'Chef Pierre is passionate about French pastry arts. A graduate of Le Cordon Bleu Paris, he creates exquisite desserts that blend tradition with creativity.',
            'certificates' => json_encode(['Le Cordon Bleu Pâtisserie Diploma', 'French Dessert Specialist Certificate'])
        ]);

$chef->assignRole($chef_role);
        $permissions = $chef_role->permissions()->pluck('name')->toArray();
        $chef->givePermissionTo($permissions);

        $chef2->assignRole($chef_role);
        $permissions = $chef_role->permissions()->pluck('name')->toArray();
        $chef2->givePermissionTo($permissions);

        $chef3->assignRole($chef_role);
        $permissions = $chef_role->permissions()->pluck('name')->toArray();
        $chef3->givePermissionTo($permissions);




        // ================ Receptions ================


        $reception = User::query()->create([
            'first_name' => 'Mohammad',
            'last_name'=>'Kalleh',
            'email' => 'mohammadkalleh@example.com',
            'password' => Hash::make('123456'),
            'mobile' => '+963988341003',
            'gendor' =>'male',
            'date_of_birth' =>'2000-12-20',
            'preferred_language' =>'en',
            'preferred_theme' =>'light',
        ]);
        $reception->reception()->create([
            'shift' =>'morning',
            'years_of_experience' => 5,
        ]);

        $reception->assignRole($reception_role);
        $permissions = $reception_role->permissions()->pluck('name')->toArray();
        $reception->givePermissionTo($permissions);

        $reception2 = User::query()->create([
            'first_name' => 'Sana',
            'last_name'=>'Khalil',
            'email' => 'sanakhalil2sd49@example.com',
            'password' => Hash::make('123456'),
            'mobile' => '+963988092323',
            'gendor' =>'female',
            'date_of_birth' =>'1995-11-02',
            'preferred_language' =>'en',
            'preferred_theme' =>'light',
        ]);
        $reception2->reception()->create([
            'shift' =>'evening',
            'years_of_experience' => 3,
        ]);


        $reception2->assignRole($reception_role);
        $permissions = $reception_role->permissions()->pluck('name')->toArray();
        $reception2->givePermissionTo($permissions);





         // ================ Customers ================

        $cutomer = User::query()->create([
            'first_name' => 'hassan',
            'last_name'=>'Morad',
            'email' => 'hassanmorad209hb67@example.com',
            'password' => Hash::make('123456'),
            'mobile' => '+963987791626',
            'gendor' =>'male',
            'date_of_birth' =>'1980-10-18',
            'preferred_language' =>'en',
            'preferred_theme' =>'light',
        ]);
        $cutomer->customer()->create([
            'person_height' =>'178',
            'person_weight' =>'70'
        ]);
        $cutomer->customer->mywallet()->create([
            'amount' => 5000,
        ]);



        $cutomer->assignRole($customer_role);
        $permissions = $customer_role->permissions()->pluck('name')->toArray();
        $cutomer->givePermissionTo($permissions);

        $customer2 = User::query()->create([
            'first_name' => 'Mohammad',
            'last_name'=>'Emad',
            'email' => 'mohmmademad1917cf2#90@example.com',
            'password' => Hash::make('123456'),
            'mobile' => '+963982786609',
            'gendor' =>'male',
            'date_of_birth' =>'2004-06-06',
            'preferred_language' =>'en',
            'preferred_theme' =>'light',
        ]);
        $customer2->customer()->create([
            'person_height' =>'160',
            'person_weight' =>'65'
        ]);
        $customer2->customer->mywallet()->create([
            'amount' => 5000,
        ]);

        $customer2->assignRole($customer_role);
        $permissions = $customer_role->permissions()->pluck('name')->toArray();
        $customer2->givePermissionTo($permissions);

$customer3 = User::query()->create([
            'first_name' => 'Feras',
            'last_name'=>'Mohmmad',
            'email' => 'ferasmohmmad1985aav989@example.com',
            'password' => Hash::make('123456'),
            'mobile' => '+963989438751',
            'gendor' =>'male',
            'date_of_birth' =>'1990-07-25',
            'preferred_language' =>'en',
            'preferred_theme' =>'light',
        ]);
        $customer3->customer()->create([
            'person_height' =>'180',
            'person_weight' =>'80'
        ]);
        $customer3->customer->mywallet()->create([
            'amount' => 5000,
        ]);

        $customer3->assignRole($customer_role);
        $permissions = $customer_role->permissions()->pluck('name')->toArray();
        $customer3->givePermissionTo($permissions);

        $customer4 = User::query()->create([
            'first_name' => 'Sara',
            'last_name'=>'Ebrahim',
            'email' => 'saraebrahimf2004@example.com',
            'password' => Hash::make('sara%trt'),
            'mobile' => '+963909528801',
            'gendor' =>'female',
            'date_of_birth' =>'1999-05-25',
            'preferred_language' =>'en',
            'preferred_theme' =>'light',
        ]);
        $customer4->customer()->create([
            'person_height' =>'150',
            'person_weight' =>'45'
        ]);

        $customer4->customer->mywallet()->create([
            'amount' => 5000,
        ]);

        // $stripe = new StripeService();

        // // 1. إنشاء الحساب في Stripe وربطه مع المستخدم
        // // إنشاء عميل Stripe
        // $stripeCustomer = $stripe->createStripeCustomer($customer4);

        // $paymentMethodId = 'pm_card_visa';

        // try {
        //     $stripe->attachPaymentMethodToCustomer($paymentMethodId, $stripeCustomer->id);

        //     $customer4->stripeData()->create([
        //         'stripe_customer_id' => $stripeCustomer->id,
        //         'default_payment_method_id' => $paymentMethodId
        //     ]);

        //     dump('Customer and payment method attached successfully.');
        // } catch (\Exception $e) {
        //     dump('Failed to attach payment method: ' . $e->getMessage());
        // }





        $customer4->assignRole($customer_role);
        $permissions = $customer_role->permissions()->pluck('name')->toArray();
        $customer4->givePermissionTo($permissions);


        $customer5 = User::query()->create([
            'first_name' => 'Esraa',
            'last_name'=>'Mostafa',
            'email' => 'esraamostafagh27@example.com',
            'password' => Hash::make('esraa%trt'),
            'mobile' => '+963944699128',
            'gendor' =>'female',
            'date_of_birth' =>'2002-06-05',
            'preferred_language' =>'en',
            'preferred_theme' =>'light',
        ]);
        $customer5->customer()->create([
            'person_height' =>'152',
            'person_weight' =>'48'
        ]);
        $customer5->customer->mywallet()->create([
            'amount' => 5000,
        ]);

        $customer5->assignRole($customer_role);
        $permissions = $customer_role->permissions()->pluck('name')->toArray();
        $customer5->givePermissionTo($permissions);
    }
}
