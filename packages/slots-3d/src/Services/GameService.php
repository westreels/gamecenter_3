<?php

namespace Packages\Slots3D\Services;

use App\Models\Game;
use App\Models\User;
use App\Services\GameService as ParentGameService;
use Exception;
use Illuminate\Support\Collection;
use Packages\Slots3D\Helpers\SlotMachine;
use Packages\Slots3D\Models\Slots3D;

class GameService extends ParentGameService
{
    protected $gameableClass = Slots3D::class;

    protected $slotMachine;

    protected $reels;
    protected $paytable;

    public function __construct(User $user)
    {
        parent::__construct($user);

        $this->reels = config('slots-3d.reels');
        $this->paytable = config('slots-3d.paytable');
        $this->slotMachine = new SlotMachine();
    }

    public function makeSecret(): string
    {
        return $this->slotMachine
            ->setReels($this->reels)
            ->spin()
            ->getPositions()
            ->implode(',');
    }

    /**
     * Deal initial set of cards
     *
     * @param $params
     */
    public function play($params): GameService
    {
        $provablyFairGame = $this->getProvablyFairGame();

        // init slot machine and shift reels
        $this->slotMachine
            ->setReels($this->reels)
//            ->setPositions(([0,8,1]))
            ->setPositions(explode(',', $provablyFairGame->secret))
            ->shift($provablyFairGame->shift_value);

        $bet = $params['bet'];
        $positions = $this->slotMachine->getPositions();

        $gameable = new $this->gameableClass();
        $gameable->positions = $positions;

        // important to save a reference to gameable in the class property, so it can be used in the parent class
        $this->gameable = $gameable;

        $win = 0;
        collect($this->paytable)
            ->each(function ($item) use ($bet, $positions, &$win) {
                if ($this->isWinningPositions(collect($item->positions), $positions)) {
                    $win = $bet * $item->payout;
                    return FALSE;
                }
            });

        $this->save([
            'bet' => $bet,
            'win' => $win,
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

        $instance->play([
            'bet' => random_int($minBet ?: config('slots-3d.min_bet'), $maxBet ?: config('slots-3d.max_bet'))
        ]);
    }

    protected function isWinningPositions(Collection $winningSymbols, Collection $actualPositions): bool
    {
        if ($winningSymbols->count() != $actualPositions->count()) {
            throw new Exception('Slot machine configuration is invalid.');
        }

        $matchesCount = $winningSymbols->reduce(function ($matchesCount, $symbol, $reel) use ($actualPositions) {
            return $matchesCount + ($symbol === NULL || $symbol === ($this->reels[$reel][$actualPositions[$reel]] ?? -1) ? 1 : 0);
        }, 0);

        return $matchesCount === $winningSymbols->count();
    }
}
