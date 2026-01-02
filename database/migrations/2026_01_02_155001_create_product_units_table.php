<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('unit_name', 20);                         // Nama satuan (Karung/Dus/Pack)
            $table->decimal('conversion_factor', 10, 2)->default(1); // Pengali ke base unit
            $table->decimal('price', 15, 2);                         // Harga jual per satuan ini
            $table->boolean('is_base')->default(false);              // Penanda satuan default
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_units');
    }
};
