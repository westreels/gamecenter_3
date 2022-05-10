<?php

namespace Packages\Keno\Services;

use App\Helpers\Games\SequenceGenerator;
use App\Models\Game;
use App\Models\User;
use App\Services\GameService as ParentGameService;
use Packages\Keno\Models\Keno;

class GameService extends ParentGameService
{
    protected $gameableClass = Keno::class;

    protected $sequenceGenerator;

    public function __construct(User $user)
    {
        parent::__construct($user);

        $this->sequenceGenerator = new SequenceGenerator((int) config('keno.draw_count'), 1, self::getMaxNumber());
    }

    public function makeSecret(): string
    {
        return $this->sequenceGenerator->generateUnique()->get()->sort()->values()->implode(',');
    }

    /**
     * Play
     *
     * @param $params
     */
    public function play($params): GameService
    {
        $provablyFairGame = $this->getProvablyFairGame();

        $numbers = $this->sequenceGenerator->set(collect(explode(',', $provablyFairGame->secret)))->shift($provablyFairGame->shift_value)->get();

        $gameable = new $this->gameableClass();
        $gameable->selected_numbers = collect($params['numbers'])->sort()->values();
        $gameable->drawn_numbers = $numbers->sort()->values();

        // important to save a reference to gameable in the class property, so it can be used in the parent class
        $this->gameable = $gameable;

        $hitsCount = $gameable->selected_numbers->intersect($gameable->drawn_numbers)->count();
        $bet = $params['bet'];
        $win = $hitsCount > 0 ? $bet * floatval(config('keno.paytable')[min($hitsCount, count(config('keno.paytable'))) - 1] ?? 0) : 0;

        $this->save([
            'bet'       => $bet,
            'win'       => $win,
            'status'    => Game::STATUS_COMPLETED
        ]);

        return $this;
    }

    public static function createRandomGame(User $user): void
    {
        $instance = new self($user);

        $instance->createProvablyFairGame(random_int(10000,99999));

        $minBet = intval(config('settings.bots.games.min_bet'));
        $maxBet = intval(config('settings.bots.games.max_bet'));

        $instance->play([
            'bet' => random_int($minBet ?: config('keno.min_bet'), $maxBet ?: config('keno.max_bet')),
            'numbers' => (new SequenceGenerator((int) config('keno.select_count'), 1, self::getMaxNumber()))->generateUnique()->get()->toArray()
        ]);
    }

    public static function getMaxNumber(): int
    {
        return intval(config('keno.rows_count')) * intval(config('keno.cols_count'));
    }
}
