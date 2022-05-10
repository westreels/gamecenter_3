<?php

namespace Packages\SicBo\Services;

use App\Models\Game;
use App\Models\User;
use App\Services\GameService as ParentGameService;
use App\Services\RandomGameService;
use Packages\SicBo\Helpers\SicBo as SicBoGame;
use Packages\SicBo\Helpers\SicBoBet;
use Packages\SicBo\Models\SicBo;

class GameService extends ParentGameService implements RandomGameService
{
    protected $gameableClass = SicBo::class;

    protected $sicBo;

    public function __construct(User $user)
    {
        parent::__construct($user);

        $this->sicBo = new SicBoGame();
    }

    public function makeSecret(): string
    {
        return $this->sicBo->rollDice()->getDice()->implode(',');
    }

    /**
     * Deal initial set of cards
     *
     * @param $params
     */
    public function play($params): GameService
    {
        $provablyFairGame = $this->getProvablyFairGame();

        $bets = $params['bets'];

        // load initially rolled dice and shift them
        $this->sicBo
            ->setDice(explode(',', $provablyFairGame->secret))
            ->shiftDice($provablyFairGame->shift_value);

        $sicBoBet = new SicBoBet($this->sicBo, $bets);

        $gameable = new $this->gameableClass();
        $gameable->bets = $sicBoBet->getBets();
        $gameable->roll = $this->sicBo->getDice();
        $gameable->score = $this->sicBo->getScore();

        // important to save a reference to gameable in the class property, so it can be used in the parent class
        $this->gameable = $gameable;

        $this->save([
            'bet' => $sicBoBet->getBetAmount(),
            'win' => $sicBoBet->getWinAmount(),
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

        $bets = [];
        $betsCount = random_int(1, 10);
        $betTypes = SicBoBet::getBetTypes();

        for ($i = 0; $i < $betsCount; $i++) {
            $numbers = [];
            $type = $betTypes->random();

            if (in_array($type, [SicBoBet::BET_SINGLE, SicBoBet::BET_DOUBLE, SicBoBet::BET_TRIPLE])) {
                $numbers = [random_int(1, 6)];
            } elseif (in_array($type, [SicBoBet::BET_TOTAL])) {
                $numbers = [random_int(4, 17)];
            } elseif (in_array($type, [SicBoBet::BET_COMBINATION])) {
                $number = random_int(1, 6);
                $number2 = random_int(1, 6);

                // ensure 2 numbers are different
                while ($number == $number2) {
                    $number2 = random_int(1, 6);
                }

                $numbers = [$number, $number2];
            }

            $bets[] = [
                'type' => $type,
                'numbers' => $numbers,
                'amount' => random_int($minBet ?: config('sic-bo.min_bet'), $maxBet ?: config('sic-bo.max_bet'))
            ];
        }

        $instance->play(['bets' => $bets]);
    }
}
