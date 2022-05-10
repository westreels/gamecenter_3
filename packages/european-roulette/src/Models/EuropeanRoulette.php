<?php

namespace Packages\EuropeanRoulette\Models;

use App\Models\Gameable;
use App\Models\ProvableGame;
use Illuminate\Database\Eloquent\Model;

class EuropeanRoulette extends Model implements ProvableGame
{
    use Gameable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'games_european_roulette';

    /**
     * This format will be used when the model is serialized to an array or JSON.
     *
     * @var array
     */
    protected $casts = [
        'bets' => 'array',
        'wins' => 'array'
    ];

    protected $appends = ['result', 'bet_titles', 'win_titles'];

    /**
     * Getter for bet_titles attribute
     *
     * @return array
     */
    public function getBetTitlesAttribute(): array
    {
        return array_combine(
            array_map(function ($key) {
                return $this->getBetTitle($key);
            }, array_keys($this->bets)),
            array_values($this->bets)
        );
    }

    /**
     * Getter for win_titles attribute
     *
     * @return array
     */
    public function getWinTitlesAttribute(): ?array
    {
        $result = NULL;

        if (!empty($this->wins)) {
            $result = array_combine(
                array_map(function ($key) {
                    return $this->getBetTitle($key);
                }, array_keys($this->wins)),
                array_values($this->wins)
            );
        }

        return $result;
    }

    /**
     * Format $gameable->result attribute
     *
     * @return string
     */
    public function getResultAttribute(): string
    {
        return $this->win_titles ? implode(', ', $this->win_titles) : __('Nothing');
    }

    private function getBetTitle($type)
    {
        if ($type == 'red') {
            return __('Red');
        } elseif ($type == 'black') {
            return __('Black');
        } elseif ($type == 'odd') {
            return __('Odd');
        } elseif ($type == 'even') {
            return __('Even');
        } elseif ($type == 'low') {
            return __('Low');
        } elseif ($type == 'high') {
            return __('High');
        } elseif ($type == 'top_line') {
            return __('Top line');
        } elseif ($type == 'trio12') {
            return __('Trio 0-1-2');
        } elseif ($type == 'trio23') {
            return __('Trio 0-2-3');
        } elseif (preg_match('#dozen([1,2,3]{1})#', $type, $matches)) {
            return __('Dozen :n1-:n2', ['n1' => ($matches[1]-1)*12 + 1, 'n2' => $matches[1] * 12]);
        } elseif (preg_match('#column([1,2,3]{1})#', $type, $matches)) {
            return __('Column :n', ['n' => $matches[1]]);
        } elseif (preg_match('#street(\d+)#', $type, $matches)) {
            return __('Street :n1-:n2', ['n1' => $matches[1], 'n2' => $matches[1] + 3]);
        } elseif (preg_match('#corner(\d+)#', $type, $matches)) {
            return __('Corner :n1,:n2,:n3,:n4', ['n1' => $matches[1], 'n2' => $matches[1] + 1, 'n3' => $matches[1] + 3, 'n4' => $matches[1] + 4]);
        } elseif (preg_match('#split(\d+)_(\d+)#', $type, $matches)) {
            return __('Split :n1-:n2', ['n1' => $matches[1], 'n2' => $matches[2]]);
        } elseif (preg_match('#straight(\d+)#', $type, $matches)) {
            return __('Straight :n', ['n' => $matches[1]]);
        } elseif (preg_match('#sixline(\d+)#', $type, $matches)) {
            return __('Six line :n1-:n2', ['n1' => $matches[1], 'n2' => $matches[1] + 5]);
        } else {
            return 'UNKNOWN';
        }
    }

    /**
     * Getter for secret_attribute attribute
     *
     * @return string
     */
    public function getSecretAttributeAttribute(): string
    {
        return 'win_number';
    }

    /**
     * Getter for secret_attribute_hint attribute
     *
     * @return string
     */
    public function getSecretAttributeHintAttribute(): string
    {
        return __('The initial roulette number is adjusted by the Shift value.');
    }
}
