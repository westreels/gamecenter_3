<?php

namespace App\Services;

use App\Events\MultiplayerGameAction;
use App\Facades\AccountTransaction;
use App\Helpers\PackageManager;
use App\Models\Account;
use App\Models\Game;
use App\Models\MultiplayerGame;
use App\Models\ProvablyFairGame;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

abstract class MultiplayerGameService
{
    private $user;
    private $multiplayerGame;
    private $game;

    /**
     * @var string - should be defined in every child class
     */
    protected $gameableClass;

    /**
     * @var string - package ID that corresponds to a given $gameableClass
     */
    protected $packageId;

    /**
     * @var int
     */
    protected $delay = 0;

    /**
     * Make a game secret (reels positions, shuffled cards deck) - before applying client seed
     *
     * @return string
     */
    abstract protected function makeSecret(): string;

    /**
     * Get action data to be broadcasted
     *
     * @param string $action
     * @return array
     */
    abstract public function getGameActionData(string $action, array $request = []): array;

    /**
     * Create a gameable model instance
     *
     * @return MultiplayerGameService
     */
    abstract protected function createGameable(): Model;

    public function __construct(User $user)
    {
        if (!$this->gameableClass) {
            throw new Exception('Gameable model should be explicitly set in the child class before calling MultiplayerGameService constructor.');
        }

        // check if a specific user is passed in, otherwise get the user from the request
        $this->user = $user->getKey() ? $user : auth()->user();

        $this->packageId = app()->make(PackageManager::class)->getPackageIdByClass($this->gameableClass);
    }

    /**
     * Create a new game
     *
     * @return MultiplayerGameService
     */
    public function init($forceNewGame = FALSE): MultiplayerGameService
    {
        $multiplayerGame = $forceNewGame ? NULL : $this->getMultiplayerGameBuilder()->first();

        if (!$multiplayerGame) {
            $provablyFairGame = $this->createProvablyFairGame();
            $gameable = $this->createGameable();

            $multiplayerGame = new MultiplayerGame();
            $multiplayerGame->start_time = $this->getCurrentTime();
            $multiplayerGame->end_time = $multiplayerGame->start_time->addSeconds($this->getBettingRoundDuration());
            $multiplayerGame->provablyFairGame()->associate($provablyFairGame);
            $multiplayerGame->gameable()->associate($gameable);
            $multiplayerGame->save();

            $multiplayerGame->setRelation('provablyFairGame', $provablyFairGame);
            $multiplayerGame->setRelation('gameable', $gameable);
        }

        $this->multiplayerGame = $multiplayerGame;

        return $this->afterInitGameHook();
    }

    protected function getMultiplayerGameBuilder(): Builder
    {
        return MultiplayerGame::select('multiplayer_games.*')
            ->where('gameable_type', $this->gameableClass)
            ->where('multiplayer_games.end_time', '>', Carbon::now()) // specify table name to prevent "Column 'end_time' in where clause is ambiguous" error
            ->with('provablyFairGame', 'gameable')
            ->orderBy('id', 'desc');
    }

    public function setDelay(int $delayInSeconds): MultiplayerGameService
    {
        $this->delay = $delayInSeconds;

        return $this;
    }

    public function getCurrentTime(): Carbon
    {
        return $this->delay > 0 ? Carbon::now()->addSeconds($this->delay) : Carbon::now();
    }

    /**
     * After init hook
     *
     * @return MultiplayerGameService
     */
    protected function afterInitGameHook(): MultiplayerGameService
    {
        return $this;
    }

    public function load(MultiplayerGame $multiplayerGame): MultiplayerGameService
    {
        $this->multiplayerGame = $multiplayerGame->loadMissing('provablyFairGame', 'gameable');

        return $this;
    }

    /**
     * @return MultiplayerGame|null
     */
    public function getMultiplayerGame(): ?MultiplayerGame
    {
        return $this->multiplayerGame;
    }

    public function setMultiplayerGame(MultiplayerGame $multiplayerGame): MultiplayerGameService
    {
        $this->multiplayerGame = $multiplayerGame;

        return $this;
    }

    /**
     * Create ProvablyFairGame model instance
     *
     * @return MultiplayerGameService
     * @throws Exception
     */
    protected function createProvablyFairGame(): ProvablyFairGame
    {
        return tap(new ProvablyFairGame(), function ($provablyFairGame) {
            $provablyFairGame->secret = $this->makeSecret();
            $provablyFairGame->client_seed = random_int(10000000, 99999999);
            $provablyFairGame->gameable_type = $this->gameableClass;
            $provablyFairGame->save();
        });
    }

    public function getProvablyFairGame(): ?ProvablyFairGame
    {
        return optional($this->getMultiplayerGame())->provablyFairGame;
    }

    protected function getBet(array $request): int
    {
        return $request['bet'] ?? 0;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(Game $game): MultiplayerGameService
    {
        $this->game = $game;

        return $this;
    }

    public function loadGame(): ?Game
    {
        $game = $this->getGameable()->games()->where('games.account_id', $this->getUserAccount()->id)->first();

        return optional($game)->setRelation('account', $this->getUserAccount());
    }

    protected function createGame(): Game
    {
        $game = new Game();
        $game->account()->associate($this->getUserAccount());
        $game->provablyFairGame()->associate($this->getProvablyFairGame());
        $game->gameable()->associate($this->getGameable());
        $game->bet = 0;
        $game->win = 0;
        $game->is_in_progress = TRUE;
        $game->save();

        return $game;
    }

    public function getGameable(): ?Model
    {
        return optional($this->getMultiplayerGame())->gameable;
    }

    public function setGameable(Model $gameable): MultiplayerGameService
    {
        $this->gameable = $gameable;

        return $this;
    }

    /**
     * Get user
     * Note that it can return NULL if the class is instantiated from a job without user context
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getUserAccount(): ?Account
    {
        return optional($this->getUser(), function ($user) {
            return $user ? $user->loadMissing('account')->account : NULL;
        });
    }

    public function config($param)
    {
        return config($this->packageId . '.' . $param);
    }

    public function getBettingRoundDuration(): int
    {
        return (int) $this->config('duration');
    }

    public function getIntervalBetweenGames(): int
    {
        return (int) $this->config('interval');
    }

    protected function createNextMultiplayerGame(): MultiplayerGameService
    {
        $this->log('CREATE NEXT MULTIPLAYER GAME');

        $childGameService = get_called_class();
        $user = $this->getUser();
        $instance = $user ? app()->makeWith($childGameService, compact('user')) : app()->make($childGameService);

        $this->getMultiplayerGame()->next = $instance
            ->setDelay($this->getIntervalBetweenGames())
            ->init(TRUE)
            ->getMultiplayerGame();

        return $this;
    }

    protected function beforeSaveBetHook(array $request): MultiplayerGameService
    {
        return $this;
    }

    protected function log(string $message): MultiplayerGameService
    {
        info(sprintf('%s | Class: %s, MultGame ID: %d, User ID: %d',
            $message,
            class_basename($this->gameableClass),
            optional($this->getMultiplayerGame())->id,
            optional($this->getUser())->id
        ));

        return $this;
    }

    public function bet(array $request): MultiplayerGameService
    {
        $game = $this->loadGame() ?: $this->createGame();
        $this->setGame($game);

        $this->log('NEW BET');

        // call beforeBet hook
        $this->beforeSaveBetHook($request);

        // make changes in a single DB transaction
        DB::transaction(function () use ($game, $request) {
            $gameable = $this->getGameable();
            $bet = $request['bet'] ?? 0;
            $game->bet += $bet;

            // make account transaction if necessary
            // important to make the transaction before the game model is saved
            AccountTransaction::create(
                $this->getUserAccount(),
                $game,
                -$bet,
                FALSE
            );

            // save current user game only
            $game->save();

            // save gameable model
            $gameable->save();
        });

        // broadcast action event
        event(new MultiplayerGameAction($this->getGameActionData('bet', $request)));

        return $this;
    }
}
