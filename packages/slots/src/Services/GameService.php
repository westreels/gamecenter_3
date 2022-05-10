<?php

namespace Packages\Slots\Services;

use App\Models\Game;
use App\Models\User;
use App\Services\GameService as ParentGameService;
use Packages\Slots\Helpers\SlotMachine;
use Packages\Slots\Models\Slots;

class GameService extends ParentGameService
{
    const LINES = [
        [1, 1, 1, 1, 1],
        [0, 0, 0, 0, 0],
        [2, 2, 2, 2, 2],
        [1, 1, 0, 1, 2],
        [1, 1, 2, 1, 0],
        [1, 0, 1, 2, 1],
        [1, 0, 1, 2, 2],
        [1, 0, 0, 1, 2],
        [1, 2, 1, 0, 1],
        [1, 2, 2, 1, 0],
        [1, 2, 1, 0, 0],
        [0, 1, 2, 1, 0],
        [0, 1, 1, 1, 2],
        [0, 0, 1, 2, 2],
        [0, 0, 1, 2, 1],
        [0, 0, 0, 1, 2],
        [2, 1, 0, 1, 2],
        [2, 1, 1, 1, 0],
        [2, 2, 1, 0, 0],
        [2, 2, 1, 0, 1]
    ];

    protected $gameableClass = Slots::class;

    protected $slotMachine;

    public function __construct(User $user)
    {
        parent::__construct($user);

        $this->slotMachine = new SlotMachine();
    }

    public function makeSecret(): string
    {
        return implode(',', $this->slotMachine->spin()->getSpins());
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
        $config = config('slots.variations')[$variation];

        // set initial spins for each reel
        $this->slotMachine
            ->setSpins(explode(',', $provablyFairGame->secret))
            ->setReels(array_map(function($reel) {
                    return count($reel);
                },
                $config->reels)
            );
        // adjust spins for each reel (provably fair)
        $this->slotMachine->shift($provablyFairGame->shift_value);

        $lines = $params['lines'];
        $bet = $params['bet'];
        $reelsPositions = $this->slotMachine->getReelsPositions();

        $gameable = new $this->gameableClass();
        $gameable->variation = $variation;
        $gameable->lines = $lines;
        $gameable->reels = $reelsPositions;

        // important to save a reference to gameable in the class property, so it can be used in the parent class
        $this->gameable = $gameable;

        $wins = [];
        $winLines = [];
        $winScatters = [];
        // only 1 scatter is supported
        $scatterSymbolIndex = array_search(TRUE, array_column($config->symbols, 'scatter')) ?: -1;
        // only 1 wild is supported
        $wildSymbolIndex = array_search(TRUE, array_column($config->symbols, 'wild')) ?: -1;

        if ($scatterSymbolIndex !== FALSE) {
            $scattersCount = 0;

            foreach ($reelsPositions as $reelIndex => $reelPosition) {
                $winScatters[$reelIndex] = [];

                for ($i = 0; $i <= 2; $i++) {
                    $reelSymbolsCount = count($config->reels[$reelIndex]);
                    $symReelPosition = $reelPosition + $i < $reelSymbolsCount ? $reelPosition + $i : $reelPosition + $i - $reelSymbolsCount;
                    if ( $config->reels[$reelIndex][$symReelPosition] == $scatterSymbolIndex) {
                        $scattersCount++;
                        $winScatters[$reelIndex][] = $symReelPosition;
                    }
                }
            }

            if ($scattersCount > 0 && $config->symbols[$scatterSymbolIndex]->payouts[$scattersCount - 1] > 0) {
                $wins['scatters'] = [
                    'matches' => $scattersCount,
                    'win' => $bet * $lines * $config->symbols[$scatterSymbolIndex]->payouts[$scattersCount - 1]
                ];
            } else {
                $winScatters = [];
            }
        }

        // loop through lines
        foreach (self::LINES as $lineIndex => $line) {
            // check on how many lines the user bets
            if ($lineIndex < $lines) {
                // adjust reel positions according to current line
                $lineReelPositions = $this->getLineReelPositions($config->reels, $reelsPositions, $line);

                // get symbols (indexes) for current line
                $lineSymbolsIndexes = array_map(function ($reelIndex, $reelPosition) use ($config) {
                    return $config->reels[$reelIndex][$reelPosition];
                }, array_keys($lineReelPositions), $lineReelPositions);

                $firstSymbolIndex = $lineSymbolsIndexes[0];
                $symbolMatchesCount = 1;

                // skip scatters
                if ($firstSymbolIndex != $scatterSymbolIndex) {
                    // check how many symbols match on the current line from left to right
                    foreach (array_slice($lineSymbolsIndexes, 1) as $symbolIndex) {
                        if ($symbolIndex != $scatterSymbolIndex && ($symbolIndex == $firstSymbolIndex || $symbolIndex == $wildSymbolIndex)) {
                            $symbolMatchesCount++;
                        } else {
                            break;
                        }
                    }
                }

                // if payout for current number of matches is greater than zero add current line to winning lines
                if ($config->symbols[$firstSymbolIndex]->payouts[$symbolMatchesCount - 1] > 0) {
                    $winLines[$lineIndex] = array_pad(array_slice($lineReelPositions, 0, $symbolMatchesCount), 5, NULL);
                    $wins['lines'][$lineIndex] = [
                        // 'symbols' => array_map(function ($i) use ($config) { return $config->symbols[$i]->image; }, $lineSymbolsIndexes),
                        'matches' => $symbolMatchesCount,
                        'win' => $bet * $config->symbols[$firstSymbolIndex]->payouts[$symbolMatchesCount - 1]
                    ];
                }
            }
        }

        $gameable->wins = $wins;

        $win = (isset($wins['scatters']) ? $wins['scatters']['win'] : 0)
            + (isset($wins['lines']) ? array_sum(array_column($wins['lines'], 'win')) : 0);

        $this->save([
            'bet' => $bet * $lines,
            'win' => $win,
            'status' => Game::STATUS_COMPLETED
        ]);

        $gameable->win_lines = $winLines;
        $gameable->win_scatters = $winScatters;

        return $this;
    }

    public static function createRandomGame(User $user): void
    {
        $instance = new self($user);

        $instance->createProvablyFairGame(random_int(10000,99999));

        $minBet = intval(config('settings.bots.games.min_bet'));
        $maxBet = intval(config('settings.bots.games.max_bet'));

        $variationsCount = count(config('slots.variations'));
        $variation = $variationsCount > 1 ? random_int(0, $variationsCount - 1) : 0;

        $instance->play([
            'variation' => $variation,
            'bet'       => random_int(
                $minBet ?: config('slots.variations')[$variation]->min_bet,
                $maxBet ?: config('slots.variations')[$variation]->max_bet
            ),
            'lines'     => random_int(1, 20)
        ]);
    }

    protected function getLineReelPositions($reels, $reelPositions, $line)
    {
        return array_map(function ($reelIndex, $reelPosition, $lineShift) use ($reels) {
            $reelSymbolsCount = count($reels[$reelIndex]);
            $return = $reelPosition + $lineShift;
            return $return > $reelSymbolsCount - 1 ? $reelPosition + $lineShift - $reelSymbolsCount : $return;
        }, array_keys($reelPositions), $reelPositions, $line);
    }
}
