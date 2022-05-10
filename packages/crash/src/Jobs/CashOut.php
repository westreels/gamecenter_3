<?php

namespace Packages\Crash\Jobs;

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
use Illuminate\Support\Carbon;
use Packages\Crash\Services\GameService;

class CashOut implements ShouldQueue, ShouldBeUniqueUntilProcessing
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
        return 'crash-cash-out-' . $this->user->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $now = Carbon::now();
        $gameable = $this->multiplayerGame->gameable;

        if ($gameable->start_time->lte($now) && $gameable->end_time->gte($now)) {
            info(sprintf('Crash game %d auto cash out by user %d', $this->multiplayerGame->id, $this->user->id));

            (new GameService($this->user))
                ->load($this->multiplayerGame)
                ->cashOut();
        }
    }
}
