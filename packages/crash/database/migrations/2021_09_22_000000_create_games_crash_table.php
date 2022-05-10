<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesCrashTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_crash', function (Blueprint $table) {
            // columns
            $table->id();
            $table->text('winners'); // players who cached out - array of objects { userId: payout }
            $table->decimal('max_payout', 6, 2);
            $table->timestamp('start_time', 3)->useCurrent();
            $table->timestamp('end_time', 3)->useCurrent();
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
        Schema::dropIfExists('games_crash');
    }
}
