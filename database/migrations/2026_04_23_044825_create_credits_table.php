<?php

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
        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('daily_sale_id')->nullable()->constrained('daily_sales')->nullOnDelete();
            $table->foreignId('fuel_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('quantity', 12, 3)->default(0);
            $table->decimal('amount', 14, 2)->default(0);
            $table->decimal('balance', 14, 2)->default(0);
            $table->date('credit_date')->default(now());
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};
