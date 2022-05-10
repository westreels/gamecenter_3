<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBscAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bsc_addresses', function (Blueprint $table) {
            // columns
            $table->id();
            $table->foreignId('user_id');
            $table->string('address', 42);
            $table->string('message', 50);
            $table->boolean('confirmed')->default(FALSE);
            $table->timestamps();
            // foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            // indexes
            $table->unique(['user_id', 'address']);
            $table->index('confirmed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bsc_addresses');
    }
}
