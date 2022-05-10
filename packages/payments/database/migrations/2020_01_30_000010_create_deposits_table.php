<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposits', function (Blueprint $table) {
            // columns
            $table->id();
            $table->foreignId('account_id');
            $table->foreignId('deposit_method_id');
            $table->string('external_id', 100)->nullable()->unique(); // external payment ID
            $table->decimal('amount', 20, 2); // amount in credits, e.g. 100.25 credits
            $table->decimal('payment_amount', 26, 8)->nullable(); // amount in payment currency, e.g. 1.1215
            $table->string('payment_currency', 20)->nullable(); // payment currency, e.g. BTC
            $table->unsignedTinyInteger('status');
            $table->mediumText('parameters')->nullable();
            $table->text('response')->nullable();
            $table->timestamps();
            // foreign keys
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('deposit_method_id')->references('id')->on('deposit_methods')->onUpdate('cascade')->onDelete('cascade');
            // indexes
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deposits');
    }
}
