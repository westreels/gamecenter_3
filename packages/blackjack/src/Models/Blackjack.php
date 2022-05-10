<?php

namespace Packages\Blackjack\Models;

use App\Helpers\Games\Card;
use App\Models\Gameable;
use App\Models\ProvableGame;
use Illuminate\Database\Eloquent\Model;

class Blackjack extends Model implements ProvableGame
{
    use Gameable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'games_blackjack';

    /**
     * The attributes that should be hidden from JSON output.
     *
     * @var array
     */
    protected $hidden = ['deck', 'dealer_hand', 'dealer_score', 'dealer_blackjack'];

    protected $appends = ['dealer_hand_draw', 'dealer_result', 'player_result', 'player_result2', 'dealer_check'];

    /**
     * This format will be used when the model is serialized to an array or JSON.
     *
     * @var array
     */
    protected $casts = [
        'deck' => 'array',
        'actions' => 'array',
        'dealer_hand' => 'array',
        'player_hand' => 'array',
        'player_hand2' => 'array',
        'bet' => 'float',
        'bet2' => 'float',
        'win' => 'float',
        'win2' => 'float',
        'insurance_bet' => 'float',
        'insurance_win' => 'float',
        'player_score' => 'integer',
        'player_score2' => 'integer',
        'dealer_blackjack' => 'boolean',
        'player_blackjack' => 'boolean',
    ];

    /**
     * Getter for result attribute
     *
     * @return string
     */
    public function getResultAttribute(): string
    {
        $result = [];

        if ($this->player_blackjack && !$this->dealer_blackjack) {
            $result[] = __('Blackjack');
        } else if (!$this->player_blackjack && $this->dealer_blackjack) {
            $result[] = __('Lose');
        } else if ($this->player_blackjack && $this->dealer_blackjack) {
            $result[] = __('Push');
        // split
        } else if ($this->player_hand2) {
            if ($this->win > $this->bet) {
                $result[] = __('1st hand win');
            } else if ($this->win < $this->bet) {
                $result[] = __('1st hand lose');
            } else {
                $result[] = __('1st hand push');
            }
            if ($this->win2 > $this->bet2) {
                $result[] = __('2nd hand win');
            } else if ($this->win2 < $this->bet2) {
                $result[] = __('2nd hand lose');
            } else {
                $result[] = __('2nd hand push');
            }
        // regular win
        } else if ($this->win > $this->bet) {
            $result[] = __('Win');
        // regular loss
        } else if ($this->win < $this->bet) {
            $result[] = __('Lose');
        // regular push
        } else {
            $result[] = __('Push');
        }

        if ($this->insurance_bet > 0 && $this->insurance_win > 0) {
            $result[] = __('Insurance win');
        } else if ($this->insurance_bet > 0 && $this->insurance_win == 0) {
            $result[] = __('Insurance loss');
        }

        return implode(', ', $result);
    }

    /**
     * Getter for dealer_result attribute
     *
     * @return string
     */
    public function getDealerResultAttribute(): string
    {
        $result = '';

        if ($this->game->is_completed) {
            if ($this->dealer_blackjack && !$this->player_blackjack) {
                $result = __('Blackjack');
            } else if ($this->dealer_score > 21) {
                $result = __('Bust');
            }
        }

        return $result;
    }

    /**
     * Getter for player_result attribute
     *
     * @return string
     */
    public function getPlayerResultAttribute(): string
    {
        $result = '';

        if ($this->game->is_completed) {
            if ($this->player_blackjack && !$this->dealer_blackjack) {
                $result = __('Blackjack');
            } else if ($this->player_score > 21) {
                $result = __('Bust');
            } else if ($this->win > $this->bet) {
                $result = __('Win');
            } else if ($this->insurance_win > 0) {
                $result = __('Insurance');
            } else if ($this->win == 0) {
                $result = __('Lose');
            } else if ($this->player_score == $this->dealer_score) {
                $result = __('Push');
            }
        }

        return $result;
    }

    /**
     * Getter for player_result2 attribute
     *
     * @return string
     */
    public function getPlayerResult2Attribute(): string
    {
        $result = '';

        if ($this->game->is_completed && $this->player_hand2) {
            if ($this->player_score2 > 21) {
                $result = __('Bust');
            } else if ($this->win2 > $this->bet2) {
                $result = __('Win');
            } else if ($this->win2 == 0) {
                $result = __('Lose');
            } else if ($this->player_score2 == $this->dealer_score) {
                $result = __('Push');
            }
        }

        return $result;
    }

    /**
     * Getter for dealer_hand_draw attribute
     *
     * @return array
     */
    public function getDealerHandDrawAttribute(): array
    {
        return [$this->dealer_hand[0], NULL];
    }

    /**
     * Getter for dealer_check attribute
     *
     * @return bool
     */
    public function getDealerCheckAttribute(): bool
    {
        if (!is_array($this->dealer_hand) || !isset($this->dealer_hand[0])) {
            return FALSE;
        }

        $card = new Card($this->dealer_hand[0]);
        return $card->value->is_ten || $card->value->is_jack || $card->value->is_queen || $card->value->is_king || $card->value->is_ace;
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
