<?php

use Packages\MultiplayerRoulette\Models\MultiplayerRoulette;

return [
    'version'               => '1.1.0',
    'categories'            => json_decode(env('GAME_MULTIPLAYER_ROULETTE_CATEGORIES', json_encode(['Roulette', 'Multiplayer']))),
    'banner'                => env('GAME_MULTIPLAYER_ROULETTE_BANNER', '/images/games/multiplayer-roulette.jpg'),
    'background'            => env('GAME_MULTIPLAYER_ROULETTE_BACKGROUND', ''),
    'min_bet'               => env('GAME_MULTIPLAYER_ROULETTE_MIN_BET', 1),
    'max_bet'               => env('GAME_MULTIPLAYER_ROULETTE_MAX_BET', 50),
    'bet_change_amount'     => env('GAME_MULTIPLAYER_ROULETTE_BET_CHANGE_AMOUNT', 1),
    'default_bet_amount'    => env('GAME_MULTIPLAYER_ROULETTE_DEFAULT_BET_AMOUNT', 1),
    'interval'              => env('GAME_MULTIPLAYER_ROULETTE_INTERVAL', 15), // delay before next betting round starts (in seconds)
    'duration'              => env('GAME_MULTIPLAYER_ROULETTE_DURATION', 60), // betting round duration (in seconds)
    'bots_enabled'          => env('GAME_MULTIPLAYER_ROULETTE_BOTS_ENABLED', TRUE),
    'paytable'              => json_decode(env('GAME_MULTIPLAYER_ROULETTE_PAYTABLE', json_encode([
        MultiplayerRoulette::BET_ZERO   => 15,
        MultiplayerRoulette::BET_RED    => 2,
        MultiplayerRoulette::BET_BLACK  => 2,
    ]))),
];
