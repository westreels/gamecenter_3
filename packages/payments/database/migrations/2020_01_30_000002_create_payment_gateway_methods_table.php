<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentGatewayMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_gateway_methods', function (Blueprint $table) {
            // columns
            $table->id();
            $table->foreignId('payment_gateway_id');
            $table->tinyInteger('type'); // in, out or both
            $table->string('code', 30);
            $table->string('name', 100);
            $table->text('parameters'); // payment gateway method parameters, required to be filled when creating a deposit / withdrawal method
            $table->text('input_parameters'); // mandatory input parameters, required to be filled when creating a deposit / withdrawal by user
            $table->string('allowed_currencies', 1000); // allowed currencies to use when creating a deposit / withdrawal method
            $table->timestamps();
            // foreign keys
            $table->foreign('payment_gateway_id')->references('id')->on('payment_gateways')->onUpdate('cascade')->onDelete('cascade');
            // indexes
            $table->index('type');
            $table->index('code');
            $table->unique(['type', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_gateway_methods');
    }
}
