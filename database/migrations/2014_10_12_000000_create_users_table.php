<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            // columns
            $table->bigIncrements('id');
            $table->unsignedBigInteger('referrer_id')->nullable();
            $table->string('name', 100)->unique();
            $table->string('email')->unique();
            $table->unsignedTinyInteger('role');
            $table->unsignedTinyInteger('status');
            $table->boolean('hide_profit')->default(FALSE);
            $table->boolean('banned_from_chat')->default(FALSE);
            $table->string('avatar', 100)->nullable();
            $table->text('permissions')->nullable(); // admin permissions
            $table->string('password');
            $table->rememberToken();
            $table->string('totp_secret', 300)->nullable();
            $table->ipAddress('last_login_from')->nullable();
            $table->dateTime('last_login_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            // indexes
            $table->index('role');
            $table->index('status');
            $table->index('last_seen_at');
            // foreign keys
            $table->foreign('referrer_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
