<?php


namespace App\Services;

use App\Models\ProvablyFairGame;
use Illuminate\Support\Carbon;


class ProvablyFairGameService
{
    public static function get($hash, $gameableType)
    {
        return ProvablyFairGame::where('hash', $hash)
            ->where('gameable_type', $gameableType)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->first();
    }
}
