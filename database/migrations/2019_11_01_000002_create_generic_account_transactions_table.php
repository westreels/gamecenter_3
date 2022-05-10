<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenericAccountTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('generic_account_transactions', function (Blueprint $table) {
            // columns
            $table->bigIncrements('id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedMediumInteger('type');
            $table->decimal('amount', 20, 2);
            $table->timestamps();
            // indexes
            $table->index('type');
            // foreign keys
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('generic_account_transactions');
    }
}
