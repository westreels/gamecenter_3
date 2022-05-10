<?php

namespace App\Console\Commands;

use App\Events\UserIsOnline;
use App\Helpers\PackageManager;
use App\Models\MultiplayerGame;
use App\Models\User;
use App\Services\RandomGameService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CreateGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create games';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(PackageManager $packageManager)
    {
        $count = random_int(config('settings.bots.games.min_count'), config('settings.bots.games.max_count'));

        // retrieve bots
        $bots = User::active()
            ->bots()
            ->inRandomOrder()
            ->limit($count)
            ->get();

        // if bots exist
        if (!$bots->isEmpty()) {
            // get all game service classes
            $gameServiceClasses = [];
            $gameParams = [];

            foreach ($packageManager->getEnabled() as $package) {
                if ($package->type == 'game') {
                    $gameServiceClass = $package->namespace . 'Services\\GameService';

                    if (class_exists($gameServiceClass) && is_a($gameServiceClass, RandomGameService::class, TRUE)) {
                        $gameServiceClasses[] = $gameServiceClass;
                    }
                } elseif ($package->type == 'prediction') {
                    $gameServiceClass = $package->namespace . 'Services\\AssetPredictionService';

                    if (class_exists($gameServiceClass) && is_a($gameServiceClass, RandomGameService::class, TRUE)) {
                        $gameServiceClasses[] = $gameServiceClass;
                    }
                }
            }

            // number of games available for play
            $n = count($gameServiceClasses);

            info($n);

            if ($n > 0) {
                // loop through bots users
                foreach ($bots as $bot) {
                    // update online status
                    event(new UserIsOnline($bot));
                    tap($bot, function ($bot) { $bot->is_online = TRUE; })->save();

                    // pick a random game
                    $i = random_int(0, $n - 1);
                    // create random game
                    call_user_func_array([$gameServiceClasses[$i], 'createRandomGame'], array_merge([$bot], $gameParams));
                }
            }
        }

        return 0;
    }
}
