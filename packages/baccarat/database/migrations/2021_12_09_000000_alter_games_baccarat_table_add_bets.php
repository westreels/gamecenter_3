<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Packages\Baccarat\Models\Baccarat;

class AlterGamesBaccaratTableAddBets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('games_baccarat', function (Blueprint $table) {
            $table->unsignedInteger('player_bet')->after('deck');
            $table->decimal('player_win', 16, 2)->after('player_bet');
            $table->unsignedInteger('banker_bet')->after('player_win');
            $table->decimal('banker_win', 16, 2)->after('banker_bet');
            $table->unsignedInteger('tie_bet')->after('banker_win');
            $table->decimal('tie_win', 16, 2)->after('tie_bet');
        });

        // migrate from the old table structure when only one type of bet was possible
        foreach (Baccarat::with('game')->lazy() as $gameable) {
            if ($gameable->game) {
                $gameable->player_bet = 0;
                $gameable->player_win = 0;
                $gameable->banker_bet = 0;
                $gameable->banker_win = 0;
                $gameable->tie_bet = 0;
                $gameable->tie_win = 0;

                if ($gameable->bet_type == Baccarat::BET_TYPE_PLAYER) {
                    $gameable->player_bet = $gameable->game->bet;
                    $gameable->player_win = $gameable->game->win;
                } elseif ($gameable->bet_type == Baccarat::BET_TYPE_BANKER) {
                    $gameable->banker_bet = $gameable->game->bet;
                    $gameable->banker_win = $gameable->game->win;
                } elseif ($gameable->bet_type == Baccarat::BET_TYPE_TIE) {
                    $gameable->tie_bet = $gameable->game->bet;
                    $gameable->tie_win = $gameable->game->win;
                }

                $gameable->save();
            }
        }

        Schema::table('games_baccarat', function (Blueprint $table) {
            $table->dropColumn('bet_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('games_baccarat', function (Blueprint $table) {
            $table->dropColumn('player_bet', 'player_win', 'banker_bet', 'banker_win', 'tie_bet', 'tie_win');
        });

        Schema::table('games_baccarat', function (Blueprint $table) {
            $table->unsignedTinyInteger('bet_type')->default(0);
        });
    }
}
