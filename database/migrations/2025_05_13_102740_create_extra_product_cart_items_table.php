<?php

use App\Models\CartItem;
use App\Models\ExtraProduct;
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
        Schema::create('extra_product_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CartItem::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ExtraProduct::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extra_product_cart_items');
    }
};
