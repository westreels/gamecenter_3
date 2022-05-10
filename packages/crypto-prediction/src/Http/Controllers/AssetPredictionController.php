<?php

namespace Packages\CryptoPrediction\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Game;
use Packages\CryptoPrediction\Http\Requests\CompleteAssetPrediction;
use Packages\CryptoPrediction\Http\Requests\MakeAssetPrediction;
use Packages\CryptoPrediction\Models\CryptoPrediction;
use Packages\CryptoPrediction\Services\AssetPredictionService;
use Illuminate\Http\Request;

class AssetPredictionController extends Controller
{
    public function index(Request $request, Asset $asset)
    {
        return tap(
            $request->user()->games()
                ->inProgress()
                ->where('gameable_type', CryptoPrediction::class)
                ->with('gameable.asset')
                ->orderBy('id', 'desc')
                ->get()
                ->filter(function ($game) use ($asset) {
                    return $game->gameable->asset->id == $asset->id;
                })
                ->first(),
            function (?Game $game) {
                if ($game) {
                    $game->append('server_time');
                }
            }
        );
    }

    public function make(MakeAssetPrediction $request, Asset $asset, AssetPredictionService $predictionService)
    {
        return $predictionService
            ->make($asset, $request->only(['bet', 'direction', 'duration']))
            ->append('server_time');
    }

    public function complete(CompleteAssetPrediction $request, Game $game, AssetPredictionService $predictionService)
    {
        return $predictionService
            ->complete($game)
            ->append('server_time');
    }
}
