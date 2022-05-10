<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliateCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliate_commissions', function (Blueprint $table) {
            // columns
            $table->bigIncrements('id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('referral_account_id');
            $table->unsignedTinyInteger('tier');
            $table->unsignedTinyInteger('type');
            $table->unsignedTinyInteger('status');
            $table->decimal('amount', 20, 2);
            $table->morphs('commissionable', 'affiliate_commission_morph'); // polymorphic relation (index is created automatically)
            $table->ipAddress('ip');
            $table->timestamps();
            // foreign keys
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('referral_account_id')->references('id')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('affiliate_commissions');
    }
}
