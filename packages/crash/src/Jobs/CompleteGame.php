<?php

namespace Packages\Crash\Jobs;

use App\Models\MultiplayerGame;
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

class CompleteGame implements ShouldQueue, ShouldBeUniqueUntilProcessing
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
    public $timeout = 30;

    /**
     * Indicate if the job should be marked as failed on timeout.
     *
     * @var bool
     */
    public $failOnTimeout = true;

    protected $multiplayerGame;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MultiplayerGame $multiplayerGame)
    {
        $this->multiplayerGame = $multiplayerGame;
    }

    /**
     * Job unique ID (required if job implements ShouldBeUniqueUntilProcessing)
     * See https://stackoverflow.com/a/66637893/2767324
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return 'crash';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(GameService $gameService)
    {
        $scheduledTime = $this->multiplayerGame->gameable->end_time;
        $now = Carbon::now();

        info(sprintf('Crash game %d completion scheduled at %s started at %s (diff %f ms)',
            $this->multiplayerGame->id,
            $scheduledTime->toDateTimeString('millisecond'),
            $now->toDateTimeString('millisecond'),
            $now->diffInMilliseconds($scheduledTime)
        ));

        $gameService
            ->load($this->multiplayerGame)
            ->complete();

        info(sprintf('Crash game %d completion finished at %s', $this->multiplayerGame->id, Carbon::now()->toDateTimeString('millisecond')));
    }
}
