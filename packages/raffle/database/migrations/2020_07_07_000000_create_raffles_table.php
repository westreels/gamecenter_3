<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRafflesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raffles', function (Blueprint $table) {
            // columns
            $table->bigIncrements('id');
            $table->unsignedBigInteger('winning_ticket_id')->nullable();
            $table->string('title');
            $table->string('description', 5000)->nullable();
            $table->string('banner')->nullable();
            $table->unsignedInteger('ticket_price');
            $table->unsignedInteger('max_tickets_per_user');
            $table->unsignedInteger('total_tickets');
            $table->decimal('fee', 5, 2);
            $table->decimal('win', 10, 2);
            $table->unsignedTinyInteger('status');
            $table->unsignedTinyInteger('completion_trigger');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->boolean('recurring');
            $table->timestamps();
            // indexes
            $table->index('status');
            $table->index('completion_trigger');
            $table->index('end_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('raffles');
    }
}
