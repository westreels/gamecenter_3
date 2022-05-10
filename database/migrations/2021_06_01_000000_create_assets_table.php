<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('type');
            $table->string('symbol', 50);
            $table->string('name', 180);
            $table->string('external_id', 100);
            $table->unsignedTinyInteger('status');
            $table->unsignedDecimal('price', 20, 8);
            $table->timestamps();
            // indexes
            $table->unique(['type', 'symbol']);
            $table->index('name');
            $table->index('status');
            $table->index('external_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assets');
    }
}
