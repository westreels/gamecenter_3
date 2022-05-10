<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepositMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1.3.x -> 1.4.x upgrade
        if (Schema::hasTable('deposit_methods')) {
            Schema::disableForeignKeyConstraints();
            Schema::drop('deposit_methods');
            Schema::enableForeignKeyConstraints();
        }

        Schema::create('deposit_methods', function (Blueprint $table) {
            // columns
            $table->id();
            $table->foreignId('payment_method_id')->nullable();
            $table->string('name', 100);
            $table->string('description', 2000)->nullable();
            $table->string('currency', 20); // price currency
            $table->decimal('rate', 12, 2); // Amount of credits per 1 unit of the price currency
            $table->boolean('enabled')->default(FALSE);
            $table->text('payment_method_parameters'); // filled payment method parameters
            $table->text('parameters'); // input parameters to be filled by user when creating deposit / withdrawal
            $table->timestamps();
            // foreign keys
            $table->foreign('payment_method_id')->references('id')->on('payment_gateway_methods')->onUpdate('cascade')->onDelete('cascade');
            // indexes
            $table->index('enabled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deposit_methods');
    }
}
