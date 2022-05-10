<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessageRecipients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_message_recipients', function (Blueprint $table) {
            // columns
            $table->bigIncrements('id');
            $table->bigInteger('message_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();
            // foreign keys
            $table->foreign('message_id')->references('id')->on('chat_messages')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            // indexes
            $table->unique(['message_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_message_recipients');
    }
}
