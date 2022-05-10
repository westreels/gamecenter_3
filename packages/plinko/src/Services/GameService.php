<?php

namespace Packages\Plinko\Services;

use App\Models\Game;
use App\Models\User;
use App\Services\GameService as ParentGameService;
use Packages\Plinko\Helpers\Plinko as PlinkoGame;
use Packages\Plinko\Models\Plinko;

class GameService extends ParentGameService
{
    protected $gameableClass = Plinko::class;

    protected $plinko;

    public function __construct(User $user)
    {
        parent::__construct($user);

        $this->plinko = new PlinkoGame(count(config('plinko.paytable')) - 1);
    }

    public function makeSecret(): string
    {
        return $this->plinko->generatePath()->getPath()->implode(',');
    }

    /**
     * Play
     *
     * @param $params
     */
    public function play($params): GameService
    {
        $provablyFairGame = $this->getProvablyFairGame();

        // load path and shift it
        $this->plinko
            ->setPath(explode(',', $provablyFairGame->secret))
            ->shiftPath($provablyFairGame->shift_value);

        $gameable = new $this->gameableClass();
        $gameable->path = $this->plinko->getPath();

        // important to save a reference to gameable in the class property, so it can be used in the parent class
        $this->gameable = $gameable;

        $bet = $params['bet'];
        $win = $bet * (config('plinko.paytable')[$gameable->path->sum()] ?? 0);

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

        $instance->play(['bet' => random_int($minBet ?: config('plinko.min_bet'), $maxBet ?: config('plinko.max_bet'))]);
    }
}
