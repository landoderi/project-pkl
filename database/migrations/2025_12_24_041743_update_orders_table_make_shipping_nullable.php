<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->string('shipping_name')->nullable()->change();
        // jika ada kolom lain seperti alamat/telepon
        // $table->string('shipping_address')->nullable()->change();
        // $table->string('shipping_phone')->nullable()->change();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
