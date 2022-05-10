<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvablyFairGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provably_fair_games', function (Blueprint $table) {
            // columns
            $table->bigIncrements('id');
            $table->string('secret', 500); // initial state of the related game, i.e. reels position, shuffled deck of cards
            $table->string('server_seed', 32); // server seed - random string
            $table->string('hash', 64); // HMAC hash of secret and seed
            $table->string('client_seed', 32);
            $table->string('gameable_type');
            $table->timestamps();
            // indexes
            $table->unique(['hash', 'gameable_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('provably_fair_games');
    }
}
