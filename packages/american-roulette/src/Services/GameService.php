<?php

namespace Packages\AmericanRoulette\Services;

use App\Models\Game;
use App\Models\User;
use App\Services\GameService as ParentGameService;
use Packages\AmericanRoulette\Helpers\Roulette;
use Packages\AmericanRoulette\Models\AmericanRoulette;

class GameService extends ParentGameService
{
    protected $gameableClass = AmericanRoulette::class;

    protected $roulette;

    public function __construct(User $user)
    {
        parent::__construct($user);

        $this->roulette = new Roulette();
    }

    public function makeSecret(): string
    {
        return $this->roulette->spin()->getNumber();
    }

    /**
     * Deal initial set of cards
     *
     * @param $params
     */
    public function play($params): GameService
    {
        $provablyFairGame = $this->getProvablyFairGame();

        // load initial random number
        $this->roulette->setNumber($provablyFairGame->secret);
        // adjust number (provably fair)
        $this->roulette->shift($provablyFairGame->shift_value);

        $bets = $params['bets'];
        $wins = $this->getWinningBets($params['bets'], $this->roulette->getNumber());

        $gameable = new $this->gameableClass();
        $gameable->bets = $bets;
        $gameable->wins = $wins;
        $gameable->win_number = $this->roulette->getNumber();

        // important to save a reference to gameable in the class property, so it can be used in the parent class
        $this->gameable = $gameable;

        $this->save([
            'bet' => array_sum($bets),
            'win' => array_sum($wins),
            'status' => Game::STATUS_COMPLETED
        ]);

        return $this;
    }

    /**
     * Get winning bets
     *
     * @param array $bets
     * @param int $winNumber
     * @return array
     */
    protected function getWinningBets(array $bets, int $winNumber): array
    {
        $result = [];

        foreach ($bets as $type => $amount) {
            if (in_array($winNumber, $this->roulette::BETS[$type])) {
                $result[$type] = $amount * (36 / count($this->roulette::BETS[$type]));
            }
        }

        return $result;
    }

    public static function createRandomGame(User $user): void
    {
        $instance = new self($user);

        $instance->createProvablyFairGame(random_int(10000,99999));

        $minBet = intval(config('settings.bots.games.min_bet'));
        $maxBet = intval(config('settings.bots.games.max_bet'));

        $bets = [];
        $betsCount = random_int(1, 10);
        $betTypes = array_keys(Roulette::BETS);
        $betTypesCount = count($betTypes);

        while (count($bets) < $betsCount) {
            $bet = $betTypes[random_int(0, $betTypesCount-1)];
            if (!array_key_exists($bet, $bets))
                $bets[$bet] = random_int($minBet ?: config('american-roulette.min_bet'), $maxBet ?: config('american-roulette.max_bet'));
        }

        $instance->play(['bets' => $bets]);
    }
}
