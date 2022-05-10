<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDepositMethodsTableAddDecimalPlaces extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deposit_methods', function (Blueprint $table) {
            $table->unsignedTinyInteger('decimal_places')->nullable()->after('rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deposit_methods', function (Blueprint $table) {
            $table->dropColumn('decimal_places');
        });
    }
}
