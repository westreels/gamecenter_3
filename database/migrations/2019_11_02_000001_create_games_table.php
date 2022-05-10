<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            // columns
            $table->bigIncrements('id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('provably_fair_game_id');
            $table->morphs('gameable', 'game_morph'); // polymorphic relation (index is created automatically)
            $table->decimal('bet', 16, 2);
            $table->decimal('win', 16, 2);
            $table->tinyInteger('status');
            $table->timestamps();
            // indexes
            $table->index('status');
            $table->index('win');
            $table->index('created_at');
            // foreign keys
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('provably_fair_game_id')->references('id')->on('provably_fair_games')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
}
