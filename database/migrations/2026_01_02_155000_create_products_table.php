<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('code')->unique(); // Barcode/SKU
            $table->string('name');
            $table->string('base_unit', 10);                 // Satuan dasar (kg/pcs/liter)
            $table->decimal('buy_price', 15, 2)->default(0); // Harga beli (HPP)
            $table->decimal('stock', 10, 2)->default(0);     // Total Stok
            $table->timestamps();
            $table->softDeletes(); // Agar data tidak hilang permanen jika dihapus
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
