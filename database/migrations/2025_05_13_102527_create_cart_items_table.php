<?php

use App\Models\Cart;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Cart::class)->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('offer_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('price_at_order',8,0);
            $table->decimal('total_price',8,0);
            $table->integer("quantity");
            $table->boolean('is_selected_for_checkout')->default(false);
            $table->boolean('is_pre_order')->default(false); // هل هذا المنتج لطلب مسبق؟
            $table->dateTime('prepare_at')->nullable(); // متى يُجهز هذا العنصر؟
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
