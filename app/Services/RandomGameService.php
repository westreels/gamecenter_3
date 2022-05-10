<?php

namespace App\Services;

use App\Models\User;

interface RandomGameService
{
    /**
     * Create a random game
     *
     * @param User $user
     */
    public static function createRandomGame(User $user): void;
}
