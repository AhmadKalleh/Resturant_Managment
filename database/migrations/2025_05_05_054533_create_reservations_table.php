<?php

use App\Models\Customer;
use App\Models\Reception;
use App\Models\Table;
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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Table::class)->constrained()->cascadeOnDelete();
            $table->dateTime("reservation_start_time");
            $table->dateTime("reservation_end_time");
            $table->enum("status", ["pending", "confirmed", "cancelled"]);
            $table->boolean("is_on_spot")->default(false);
            $table->string("guest_name")->nullable();
            $table->string("guest_mobile")->nullable();
            $table->foreignIdFor(Reception::class,'created_by')->nullable()->constrained('receptions')->nullOnDelete();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
