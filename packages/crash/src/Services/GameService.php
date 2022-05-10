<?php

namespace Packages\Crash\Services;

use App\Events\GamePlayed;
use App\Events\MultiplayerGameAction;
use App\Facades\AccountTransaction;
use App\Models\Game;
use App\Models\MultiplayerGame;
use App\Models\User;
use App\Services\MultiplayerGameService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Packages\Crash\Jobs\Bet;
use Packages\Crash\Jobs\CompleteGame;
use Packages\Crash\Models\Crash;

class GameService extends MultiplayerGameService
{
    protected $gameableClass = Crash::class;

    protected function getMultiplayerGameBuilder(): Builder
    {
        return parent::getMultiplayerGameBuilder()
            ->join('games_crash', 'games_crash.id', '=', 'multiplayer_games.gameable_id')
            ->orWhere('games_crash.end_time', '>=', Carbon::now());
    }

    protected function makeSecret(): string
    {
        return '';
    }

    protected function createGameable(): Model
    {
        return tap(new $this->gameableClass(), function ($gameable) {
            $payoutIntervals = collect($this->config('payout_intervals'));
            $maxPayoutIndex = random_int(0, $payoutIntervals->count() - 1);
            $minPayout = $maxPayoutIndex > 0 ? round($payoutIntervals->get($maxPayoutIndex - 1) + 0.01, 2) : 1.01;
            $maxPayout = round($payoutIntervals->get($maxPayoutIndex), 2);
            $this->log(sprintf('Payout interval: %.2f - %.2f', $minPayout, $maxPayout));
            $payout = random_int($minPayout * 100, $maxPayout * 100) / 100; // min payout 1.01

            $gameable->winners = collect();
            $gameable->max_payout = 0; // do not store max payout yet, it will be re-calculated again when the completion event is initiated
            $gameable->start_time = $this->getCurrentTime()->addSeconds($this->getBettingRoundDuration() + 1); // multiplayerGame->end_time + 1 second
            $gameable->end_time = $gameable->start_time->addMilliseconds($this->calculateDuration($payout));
            $gameable->save();
        });
    }

    protected function afterInitGameHook(): MultiplayerGameService
    {
        $this->log('DISPATCH JOB');

        $gameable = $this->getGameable();
        $multiplayerGame = $this->getMultiplayerGame();

        // schedule game completion
        CompleteGame::dispatch($this->getMultiplayerGame())
            ->onQueue(STAKE_QUEUE_MULTIPLAYER_GAMES)
            ->delay($gameable->end_time);

        // add bots to the game
        if ($this->config('bots_enabled')) {
            $botsCount = random_int(config('settings.bots.games.min_count'), config('settings.bots.games.max_count'));

            User::active()
                ->bots()
                ->inRandomOrder()
                ->limit($botsCount)
                ->get()
                ->each(function ($user) use ($multiplayerGame) {
                    $duration = $multiplayerGame->end_time->diffInMilliseconds($multiplayerGame->start_time);

                    if ($duration >= 1000) {
                        Bet::dispatch($multiplayerGame, $user)
                            ->onQueue(STAKE_QUEUE_MULTIPLAYER_GAMES)
                            ->delay($multiplayerGame->start_time->addMilliseconds(random_int(1000, $duration)));
                    }
                });
        }

        return $this;
    }

    public function cashOut(): MultiplayerGameService
    {
        $this->log('CASH OUT');

        $game = $this->loadGame();
        $this->setGame($game);
        $gameable = $this->getGameable();

        // make changes in a single DB transaction
        DB::transaction(function () use ($game, $gameable) {
            $payout = round($this->calculatePayout($this->getGameable()->start_time, Carbon::now()), 2);

            $winners = $gameable->winners;
            $winners->put($this->getUser()->id, $payout);

            $gameable->winners = $winners;
            $game->win = round($game->bet * $payout, 2);
            $game->is_completed = TRUE;

            // make account transaction if necessary
            AccountTransaction::create(
                $game->account,
                $game,
                $game->win,
                FALSE
            );

            $game->save();
            $gameable->save();
        });

        // throw new GamePlayed event
        event(new GamePlayed($game));

        // broadcast action event
        event(new MultiplayerGameAction($this->getGameActionData('cash-out')));

        return $this;
    }

    public function complete(): MultiplayerGameService
    {
        $this->log('COMPLETE');

        $gameable = $this->getGameable();

        DB::transaction(function () use ($gameable) {
            // complete all user games
            $gameable->games()->inProgress()->get()->each(function (Game $game) {
                $game->is_completed = TRUE;
                $game->save();

                // throw new GamePlayed event
                event(new GamePlayed($game));
            });

            $gameable->max_payout = min($this->calculatePayout($gameable->start_time, Carbon::now()), 9999); // prevent "out of range value for column" error
            $gameable->save();
        });

        $this->createNextMultiplayerGame();

        // broadcast action event
        event(new MultiplayerGameAction($this->getGameActionData('complete')));

        return $this;
    }

    /**
     * Calculate payout for given start and end times
     *
     * @param  Carbon  $startTime
     * @param  Carbon  $endTime
     * @return float
     */
    protected function calculatePayout(Carbon $startTime, Carbon $endTime): float
    {
        $duration = $endTime->diffInMilliseconds($startTime) / 1000; // in seconds
        $payout = round(pow($this->config('base_number'), $duration), 2);

        return is_infinite($payout) ? 0 : $payout;
    }

    /**
     * Calculate duration in millisecond for given payout
     *
     * @param  float  $payout
     * @return int
     */
    protected function calculateDuration(float $payout): int
    {
        return (int) floor(log($payout, $this->config('base_number')) * 1000);
    }

    public function getGameActionData(string $action,  array $request = []): array
    {
        $userProperties = ['id', 'name', 'avatar', 'avatar_url'];

        $players = $this->getGameable()->games->loadMissing('account.user')->map(function ($game) use ($userProperties) {
                $user = $game->account->user;

                return array_merge($user->only(...$userProperties), [
                    'bet'       => $game->bet,
                    'win'       => $game->win,
                    'payout'    => $this->getGameable()->winners[$user->id] ?? 0
                ]);
            })
            ->keyBy('id');

        if ($action == 'complete') {
            $this->getGameable()->makeVisible('max_payout');
        }

        // it's important to unset games relation to prevent account data leaking
        $this->getGameable()->unsetRelation('games');

        return [
            'package_id'        => $this->packageId,
            'action'            => $action,
            'time'              => (int) Carbon::now()->valueOf(),
            'user'              => optional($this->getUser())->only(...$userProperties), // getUser() will return NULL when complete() method is called by the job
            'request'           => $request,
            'multiplayer_game'  => $this->getMultiplayerGame(),
            'players'           => $players
        ];
    }
}
