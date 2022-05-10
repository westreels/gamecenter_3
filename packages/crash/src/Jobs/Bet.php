<?php

namespace Packages\Crash\Jobs;

use App\Events\UserIsOnline;
use App\Models\MultiplayerGame;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Packages\Crash\Services\GameService;

class Bet implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 5;

    /**
     * Indicate if the job should be marked as failed on timeout.
     *
     * @var bool
     */
    public $failOnTimeout = true;

    protected $multiplayerGame;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MultiplayerGame $multiplayerGame, User $user)
    {
        $this->multiplayerGame = $multiplayerGame;
        $this->user = $user;
    }

    /**
     * Job unique ID (required if job implements ShouldBeUniqueUntilProcessing)
     * See https://stackoverflow.com/a/66637893/2767324
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return 'crash-bet-' . $this->user->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->multiplayerGame->is_betting_in_progress) {
            info(sprintf('Crash game %d new auto bet by user %d', $this->multiplayerGame->id, $this->user->id));

            // update online status
            event(new UserIsOnline($this->user));
            tap($this->user, function ($user) { $user->is_online = TRUE; })->save();

            $minBet = intval(config('settings.bots.games.min_bet'));
            $maxBet = intval(config('settings.bots.games.max_bet'));

            (new GameService($this->user))
                ->load($this->multiplayerGame)
                ->bet([
                    'bet' => random_int($minBet ?: config('crash.min_bet'), $maxBet ?: config('crash.max_bet'))
                ]);

            $gameable = $this->multiplayerGame->gameable;
            $duration = $gameable->end_time->diffInMilliseconds($gameable->start_time);

            if ($duration >= 1000 && random_int(0, 1)) {
                CashOut::dispatch($this->multiplayerGame, $this->user)
                    ->onQueue(STAKE_QUEUE_MULTIPLAYER_GAMES)
                    ->delay($gameable->start_time->addMilliseconds(random_int(1000, $duration)));
            }
        }
    }
}
