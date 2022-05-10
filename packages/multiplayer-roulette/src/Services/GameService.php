<?php

namespace Packages\MultiplayerRoulette\Services;

use App\Events\GamePlayed;
use App\Events\MultiplayerGameAction;
use App\Facades\AccountTransaction;
use App\Models\User;
use App\Services\MultiplayerGameService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Packages\MultiplayerRoulette\Helpers\Roulette;
use Packages\MultiplayerRoulette\Jobs\Bet;
use Packages\MultiplayerRoulette\Models\MultiplayerRoulette;

class GameService extends MultiplayerGameService
{
    protected $gameableClass = MultiplayerRoulette::class;

    protected $roulette;

    public function __construct(User $user)
    {
        parent::__construct($user);

        $this->roulette = new Roulette();
    }

    protected function makeSecret(): string
    {
        return $this->roulette->spin()->getNumber();
    }

    protected function createGameable(): Model
    {
        return tap(new $this->gameableClass(), function ($gameable) {
            $gameable->bets = collect();
            $gameable->save();
        });
    }

    protected function afterInitGameHook(): MultiplayerGameService
    {
        $this->log('DISPATCH JOB');

        $multiplayerGame = $this->getMultiplayerGame();

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

    protected function beforeSaveBetHook(array $request): MultiplayerGameService
    {
        $gameable = $this->getGameable();
        $bets = $gameable->bets->toArray();

        $key = $this->getUser()->id . '.' . $request['type'] . '.bet';
        data_set($bets, $key, data_get($bets, $key, 0) + $request['bet']);
        $gameable->bets = $bets;

        return $this;
    }

    public function complete(): MultiplayerGameService
    {
        $gameable = $this->getGameable();
        $userGames = $gameable->games()->with('account')->get();

        // if no one made a bet
        if ($userGames->count() == 0) {
            // create next multiplayer game
            $this->createNextMultiplayerGame();

            // broadcast action event
            event(new MultiplayerGameAction($this->getGameActionData(__FUNCTION__)));
            // if there were bets in this game
        } elseif ($userGames->where('is_in_progress', TRUE)->count() > 0) {
            $provablyFairGame = $this->getProvablyFairGame();

            // load initial random number
            $this->roulette->setNumber($provablyFairGame->secret);
            // adjust number (provably fair)
            $this->roulette->shift($provablyFairGame->shift_value);
            $gameable->win_number = $this->roulette->getNumber();

            // make changes in a single DB transaction
            DB::transaction(function () use ($gameable, $userGames) {
                // loop through all individual games
                $userGames->each(function ($game) use ($gameable) {
                    $this->log('COMPLETE');

                    // update players bets
                    $bets = $gameable->bets->toArray();

                    foreach ($bets[$game->account->user_id] as $type => &$item) {
                        $win = $this->getWinAmount($type, $item['bet']);
                        $item['win'] = $win;
                        $game->win += $win;
                    }

                    $gameable->bets = $bets;

                    $game->is_completed = TRUE;

                    // make account transaction if necessary
                    // important to make the transaction before the game model is saved
                    AccountTransaction::create(
                        $game->account,
                        $game,
                        $game->win - $game->getOriginal('win'),
                        FALSE
                    );

                    $game->save();

                    if ($game->account_id == $this->getUserAccount()->id) {
                        $this->setGame($game);
                    }

                    // throw new GamePlayed event
                    event(new GamePlayed($game));
                });

                // save gameable
                $gameable->save();

                // create next multiplayer game
                $this->createNextMultiplayerGame();
            });

            // broadcast action event
            event(new MultiplayerGameAction($this->getGameActionData(__FUNCTION__)));
            // if the linked games are already processed
        } elseif ($game = $userGames->where('account_id', $this->getUserAccount()->id)->first()) {
            $this->setGame($game);
        }

        return $this;
    }

    /**
     * Calculate win amount for a given bet
     *
     * @param string $type
     * @param int $bet
     * @return float
     */
    protected function getWinAmount(string $type, int $bet): float
    {
        $roulettePosition = $this->roulette->getPosition();

        $isZeroWin = $type == MultiplayerRoulette::BET_ZERO && $roulettePosition == 0;
        $isRedWin = $type == MultiplayerRoulette::BET_RED && $roulettePosition != 0 && $roulettePosition % 2 == 1;
        $isBlackWin = $type == MultiplayerRoulette::BET_BLACK && $roulettePosition != 0 && $roulettePosition % 2 == 0;

        if ($isZeroWin || $isRedWin || $isBlackWin) {
            return $bet * $this->config('paytable')->$type;
        }

        return 0;
    }

    public function getGameActionData(string $action,  array $request = []): array
    {
        $userProperties = ['id', 'name', 'avatar', 'avatar_url'];

        $players = $this->getGameable()->games->loadMissing('account.user')->map(function ($game) use ($userProperties) {
                return $game->account->user->only(...$userProperties);
            })
            ->keyBy('id');

        // it's important to unset games relation to prevent account data leaking
        $this->getGameable()->unsetRelation('games');

        return [
            'package_id'        => $this->packageId,
            'action'            => $action,
            'time'              => (int) Carbon::now()->valueOf(),
            'user'              => $this->getUser()->only(...$userProperties),
            'request'           => $request,
            'multiplayer_game'  => $this->getMultiplayerGame(),
            'players'           => $players
        ];
    }
}
