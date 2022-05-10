<?php

namespace App\Services;

use App\Events\GamePlayed;
use App\Facades\AccountTransaction;
use App\Helpers\Api\CryptoApi;
use App\Helpers\PackageManager;
use App\Models\Account;
use App\Models\Asset;
use App\Models\AssetPrediction;
use App\Models\Game;
use App\Models\ProvablyFairGame;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\CallApiTraits;


abstract class AssetPredictionService implements RandomGameService
{
    use CallApiTraits;
    /**
     * @var CryptoApi
     */
    protected $api;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var AssetPrediction
     */
    protected $gameableClass;

    public function __construct(User $user, CryptoApi $api)
    {
        $this->api = $api;
        // check if a specific user is passed in, otherwise get the user from the request
        $this->user = $user->getKey() ? $user : auth()->user();
    }

    public function createProvablyFairGame(): ProvablyFairGame
    {
        return tap(new ProvablyFairGame(), function ($provablyFairGame) {
            $provablyFairGame->secret = 0;
            $provablyFairGame->client_seed = 0;
            $provablyFairGame->gameable_type = AssetPrediction::class;
            $provablyFairGame->save();
        });
    }

    public function make(Asset $asset, array $params): Game
    {
        info(sprintf('New prediction for asset %d (%s) by %d (%s)', $asset->id, $asset->symbol, $this->user->id, $this->user->name ));

        if ($price = $this->api->getPrice($asset)) {
            $asset->update(['price' => $price]);
            info(sprintf('Use open price %f', $price));
        } else {
            Log::error(sprintf('Can not pull current market price for %d (%s)', $asset->id, $asset->symbol));
        }

        return DB::transaction(function () use ($asset, $params) {
            $prediction = new $this->gameableClass();
            $prediction->asset()->associate($asset);
            $prediction->direction = $params['direction'];
            $prediction->open_price = $asset->price;
            $prediction->start_time = Carbon::now();
            $prediction->end_time = Carbon::now()->addSeconds($params['duration']);
            $prediction->save();
            $account = Account::where('user_id', $this->user->id)->first();

            $game = new Game();
            $game->bet = $params['bet'];
            $game->account_id = $account->id;
            $game->provablyFairGame()->associate($this->createProvablyFairGame());
            $game->gameable()->associate($prediction);
            $game->save();
            
            AccountTransaction::create(
                $this->user->account,
                $game,
                -$game->bet,
                FALSE
            );

            return $game;
        });
    }

    public function complete(Game $game): Game
    {
        $url = config('define.api_balance.domain') . '/api/get-balance/social/' . $this->user->social_id;

        $body = $this->callapi('GET', $url, []);

        $closePrice = NULL;
        $account = $game->account;
        $account->balance = $body['balance'];
        $prediction = $game->gameable;
        $asset = $prediction->asset;

        info(sprintf('Completing prediction %d for asset %d (%s)', $prediction->id, $asset->id, $asset->symbol ));

        if (Carbon::now()->diffInSeconds($prediction->end_time) <= 5) {
            $closePrice = $this->api->getPrice($asset);
            info(sprintf('Use last close price %f', $closePrice));
        } elseif ($history = $this->api->getHistory($asset, $prediction->end_time->subMinutes(10)->timestamp * 1000, $prediction->end_time->addMinutes(10)->timestamp * 1000)) {
            $start = $prediction->end_time->timestamp * 1000;
            $end = $prediction->end_time->addMinute()->timestamp * 1000;
            $history->each(function ($item) use (&$closePrice, $prediction, $start, $end) {
                if ($start < $item->date && $item->date <= $end) {
                    $closePrice = $item->value;
                    info(sprintf('Use historical close price %f at %s', $closePrice, Carbon::createFromTimestampMsUTC($item->date)->toString()));
                    return FALSE;
                }
            });
        }

        if ($closePrice) {
            $asset->update(['price' => $closePrice]);
        } else {
            Log::error(sprintf('Can not pull close price for %d (%s)', $asset->id, $asset->symbol));
        }

        return DB::transaction(function () use ($account, $asset, $game, $prediction) {
            $packageManager = app()->make(PackageManager::class);
            $packageId = $packageManager->getPackageIdByClass($this->gameableClass);

            $win = ($asset->price - $prediction->open_price) * $prediction->direction > 0
                ? $game->bet * (float) config($packageId . '.paytable')->{$prediction->direction}
                : 0;

            $game->win = $win;
            $game->is_completed = TRUE;
            $game->save();

            $prediction->close_price = $asset->price;
            $prediction->save();

            AccountTransaction::create(
                $account,
                $game,
                $game->win,
                FALSE
            );

            $user = Auth::user();

            // throw new GamePlayed event
            event(new GamePlayed($game, $user));
            return $game;
        });
    }
}
