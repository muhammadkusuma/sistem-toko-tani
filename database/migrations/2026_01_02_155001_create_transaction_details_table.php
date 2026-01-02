<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products'); // Jangan cascade, pakai softDeletes di produk
            $table->foreignId('product_unit_id')->nullable()->constrained('product_units');

            $table->decimal('qty', 10, 2);
            $table->decimal('price', 15, 2);               // Harga jual saat itu
            $table->decimal('conversion_snapshot', 10, 2); // Nilai konversi saat itu
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaction_details');
    }
};
