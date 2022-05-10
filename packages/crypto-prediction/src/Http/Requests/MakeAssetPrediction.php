<?php

namespace Packages\CryptoPrediction\Http\Requests;

use App\Http\Requests\MakeAssetPrediction as ParentMakeAssetPrediction;
use Packages\CryptoPrediction\Models\CryptoPrediction;

class MakeAssetPrediction extends ParentMakeAssetPrediction
{
    protected $gameableClass = CryptoPrediction::class;

    public function authorize()
    {
        $maxRank = (int) config('crypto-prediction.asset_max_rank');

        return parent::authorize() && $this->asset->rank <= $maxRank;
    }
}
