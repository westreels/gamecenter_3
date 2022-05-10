<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesDiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_dice', function (Blueprint $table) {
            // columns
            $table->bigIncrements('id');
            $table->tinyInteger('direction')->nullable(); // -1 (low) or 1 (high)
            $table->decimal('win_chance', 5, 2)->nullable(); // win probability in %
            $table->decimal('payout', 10, 4)->nullable();
            $table->integer('ref_number')->nullable()->unsigned(); // reference number to compare the roll with
            $table->integer('roll')->nullable()->unsigned(); // roll result number
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
        Schema::dropIfExists('games_dice');
    }
}
