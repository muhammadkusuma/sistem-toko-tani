<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // Asumsi tabel users sudah ada (bawaan Laravel). Nullable jika kasir dihapus, history tetap ada.
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('invoice_no')->unique();
            $table->decimal('total_amount', 15, 2);
            $table->decimal('cash_amount', 15, 2);
            $table->decimal('change_amount', 15, 2); // Kembalian
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
