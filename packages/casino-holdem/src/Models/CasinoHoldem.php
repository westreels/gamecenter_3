<?php

namespace Packages\CasinoHoldem\Models;

use App\Casts\CardSet;
use App\Models\Gameable;
use App\Models\ProvableGame;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Packages\CasinoHoldem\Helpers\PokerHand;

class CasinoHoldem extends Model implements ProvableGame
{
    use Gameable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'games_casino_holdem';

    /**
     * The attributes that should be hidden from JSON output.
     *
     * @var array
     */
    protected $hidden = ['deck', 'dealer_cards', 'dealer_hand', 'dealer_kickers', 'dealer_hand_rank'];

    protected $appends = [
        'player_combination_cards',
        'dealer_combination_cards',
        'player_hand_title',
        'dealer_hand_title',
        'player_bonus_hand_title'
    ];

    /**
     * This format will be used when the model is serialized to an array or JSON.
     *
     * @var array
     */
    protected $casts = [
        'deck'                  => 'collection',
        'player_cards'          => CardSet::class,
        'dealer_cards'          => CardSet::class,
        'community_cards'       => CardSet::class,
        'player_hand'           => CardSet::class,
        'player_bonus_hand'     => CardSet::class,
        'player_kickers'        => CardSet::class,
        'dealer_hand'           => CardSet::class,
        'dealer_kickers'        => CardSet::class,
        'dealer_qualified'      => 'boolean',
    ];

    /**
     * Getter for result attribute
     *
     * @return string
     */
    public function getResultAttribute(): string
    {
        $result = collect();

        return $result->implode(', ');
    }

    /**
     * Getter for player_combination_cards
     *
     * @return Collection
     */
    public function getPlayerCombinationCardsAttribute(): ?Collection
    {
        return $this->player_hand && $this->player_kickers && $this->player_kickers->count() > 0
            ?  $this->player_hand->take(5 - $this->player_kickers->count())
            : $this->player_hand;
    }

    /**
     * Getter for dealer_combination_cards
     *
     * @return Collection
     */
    public function getDealerCombinationCardsAttribute(): ?Collection
    {
        return $this->dealer_hand && $this->dealer_kickers && $this->dealer_kickers->count() > 0
            ?  $this->dealer_hand->take(5 - $this->dealer_kickers->count())
            : $this->dealer_hand;
    }

    public function getPlayerHandTitleAttribute(): ?string
    {
        return is_int($this->player_hand_rank) ? PokerHand::getCombinationTitle($this->player_hand_rank) : NULL;
    }

    public function getDealerHandTitleAttribute(): ?string
    {
        return !$this->dealer_qualified
            ? __('Not qualified')
            : (is_int($this->dealer_hand_rank) ? PokerHand::getCombinationTitle($this->dealer_hand_rank) : NULL);
    }

    public function getPlayerBonusHandTitleAttribute(): ?string
    {
        return is_int($this->player_bonus_hand_rank) ? PokerHand::getCombinationTitle($this->player_bonus_hand_rank) : NULL;
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
