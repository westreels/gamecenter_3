<?php

namespace Packages\CryptoPrediction\Models;

use App\Models\AssetPrediction;

class CryptoPrediction extends AssetPrediction
{
    public function getTitleAttribute()
    {
        return __('Crypto prediction');
    }
}
