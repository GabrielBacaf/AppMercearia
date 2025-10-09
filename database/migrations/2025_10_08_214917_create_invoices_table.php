<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->longText('file');
            $table->timestamps();
        });
    }

 
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
