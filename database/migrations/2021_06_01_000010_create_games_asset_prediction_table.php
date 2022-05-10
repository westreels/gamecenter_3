<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesAssetPredictionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_asset_prediction', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->tinyInteger('direction'); // -1 (sell) or 1 (buy)
            $table->decimal('open_price', 18, 8);
            $table->decimal('close_price', 18, 8)->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->timestamps();
            // indexes
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
        Schema::dropIfExists('games_asset_prediction');
    }
}
