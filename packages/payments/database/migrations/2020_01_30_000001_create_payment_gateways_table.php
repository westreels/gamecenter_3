<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1.3.x -> 1.4.x upgrade
        if (Schema::hasTable('payment_gateways')) {
            Schema::disableForeignKeyConstraints();
            Schema::drop('payment_gateways');
            Schema::enableForeignKeyConstraints();
        }

        Schema::create('payment_gateways', function (Blueprint $table) {
            // columns
            $table->id('id');
            $table->string('code', 30)->unique();
            $table->string('name', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_gateways');
    }
}
