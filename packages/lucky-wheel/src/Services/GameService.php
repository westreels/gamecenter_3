<?php

namespace Packages\LuckyWheel\Services;

use App\Helpers\Games\NumberGenerator;
use App\Models\Game;
use App\Models\User;
use App\Services\GameService as ParentGameService;
use Packages\LuckyWheel\Models\LuckyWheel;

class GameService extends ParentGameService
{
    protected $gameableClass = LuckyWheel::class;

    protected $numberGenerator;

    public function __construct(User $user)
    {
        parent::__construct($user);

        $this->numberGenerator = new NumberGenerator();
    }

    public function makeSecret(): string
    {
        return $this->numberGenerator->generate()->getNumber();
    }

    /**
     * Deal initial set of cards
     *
     * @param $params
     */
    public function play($params): GameService
    {
        $provablyFairGame = $this->getProvablyFairGame();

        $variation = $params['variation'];
        $config = config('lucky-wheel.variations')[$variation];

        // adjust spins for each reel (provably fair)
        $this->numberGenerator->setMax(count($config->sections) - 1)
            ->setNumber($provablyFairGame->secret)
            ->shift($provablyFairGame->shift_value);

        $bet = $params['bet'];

        $gameable = new $this->gameableClass();
        $gameable->variation = $variation;
        $gameable->position = $this->numberGenerator->getNumber();

        // important to save a reference to gameable in the class property, so it can be used in the parent class
        $this->gameable = $gameable;

        $this->save([
            'bet' => $bet,
            'win' => $bet * $config->sections[$gameable->position]->payout,
            'status' => Game::STATUS_COMPLETED
        ]);

        return $this;
    }

    public static function createRandomGame(User $user): void
    {
        $instance = new self($user);

        $instance->createProvablyFairGame(random_int(10000,99999));

        $minBet = intval(config('settings.bots.games.min_bet'));
        $maxBet = intval(config('settings.bots.games.max_bet'));

        $variationsCount = count(config('lucky-wheel.variations'));
        $variation = $variationsCount > 1 ? random_int(0, $variationsCount - 1) : 0;

        $instance->play([
            'variation' => $variation,
            'bet'       => random_int(
                $minBet ?: config('lucky-wheel.variations')[$variation]->min_bet,
                $maxBet ?: config('lucky-wheel.variations')[$variation]->max_bet
            )
        ]);
    }
}
