<?php

namespace Packages\MultiplayerBlackjack\Models;

use App\Models\Gameable;
use App\Models\ProvableGame;
use Illuminate\Database\Eloquent\Model;

class MultiplayerBlackjack extends Model implements ProvableGame
{
    use Gameable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'games_multiplayer_blackjack';

    /**
     * The attributes that should be hidden from JSON output.
     *
     * @var array
     */
    protected $hidden = ['deck', 'hands'];

    protected $appends = ['player_hand', 'opponent_hands'];

    /**
     * This format will be used when the model is serialized to an array or JSON.
     *
     * @var array
     */
    protected $casts = [
        'deck' => 'array',
        'hands' => 'collection',
    ];

    /**
     * Getter for result attribute
     *
     * @return string
     */
    public function getResultAttribute(): string
    {
        return '';
    }

    /**
     * Getter for player_hand attribute
     *
     * @return array
     */
    public function getPlayerHandAttribute()
    {
        $user = auth()->user();

        return $this->hands->has($user->id) ? $this->hands->get($user->id) : NULL;
    }

    /**
     * Getter for opponent_hands attribute
     *
     * @return array
     */
    public function getOpponentHandsAttribute()
    {
        $user = auth()->user();

        $result = $this->hands
            ->filter(function ($hand, $userId) use ($user) {
                return $user->id != $userId;
            })
            ->map(function ($hand, $userId) {
                return [
                    'bet' => $hand['bet'],
                    // show only 1st card to other players while the game is in progress
                    'cards' => $this->is_completed ? $hand['cards'] : [$hand['cards'][0]] + array_pad([], count($hand['cards']), NULL),
                    // don't show the hand score while the game is in progress
                    'score' => $this->is_completed ? $hand['score'] : -1,
                    // don't show the win amount score while the game is in progress
                    'win' => $this->is_completed ? $hand['win'] : 0,
                    'action_start' => $hand['action_start'],
                    'action_end' => $hand['action_end'],
                ];
            })
            ->toArray();

        return !empty($result) ? $result : NULL;
    }

    /**
     * Getter for secret_attribute attribute
     *
     * @return string
     */
    public function getSecretAttributeAttribute(): string
    {
        return 'deck';
    }

    /**
     * Getter for secret_attribute_hint attribute
     *
     * @return string
     */
    public function getSecretAttributeHintAttribute(): string
    {
        return __('The first N cards are removed from the top of the deck and placed under the remaining cards. N is the remainder of dividing the Shift value by 52.');
    }
}
