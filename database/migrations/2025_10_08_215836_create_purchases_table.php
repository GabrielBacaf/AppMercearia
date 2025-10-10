<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('title', 50);
            $table->string('description')->nullable();
            $table->decimal('value', 10, 2);
            $table->decimal('count_value')->nullable();
            $table->date('purchase_date');
            $table->string('status');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            $table->foreignId('invoice_id')->nullable()->constrained('invoices');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
