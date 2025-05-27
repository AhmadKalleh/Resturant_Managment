<?php

use App\Models\Chef;
use App\Models\User;
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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Chef::class,'created_by')->constrained('chefs')->cascadeOnDelete();
            $table->json('title');
            $table->json('description')->nullable();
            $table->decimal('total_price',8,0);
            $table->decimal('price_after_discount',8,0);
            $table->string('discount_value');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
