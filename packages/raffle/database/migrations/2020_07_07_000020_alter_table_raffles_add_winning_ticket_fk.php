<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableRafflesAddWinningTicketFk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('raffles', function (Blueprint $table) {
            $table->foreign('winning_ticket_id')->references('id')->on('raffle_tickets')
                ->onUpdate('set null')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('raffles', function (Blueprint $table) {
            $table->dropForeign(['winning_ticket_id']);
            $table->dropColumn('winning_ticket_id');
        });
    }
}
