<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Helpers\Games;

use App\Helpers\MagicGetters;
use Illuminate\Support\Collection;

class CardSuit
{
    use MagicGetters;

    // D diamonds (♦), C clubs (♣), H hearts (♥), S spades (♠)
    protected const SUITS = [
        'C', 'D', 'H', 'S'
    ];

    protected $code;

    public function __construct(string $code)
    {
        if (!in_array($code, self::SUITS)) {
            throw new \Exception(sprintf('Suit "%s" is not supported.', $code));
        }

        $this->code = $code;

        return $this;
    }

    protected function getRank(): int
    {
        return array_search($this->code, self::SUITS, TRUE);
    }

    protected function getCode(): string
    {
        return $this->code;
    }

    protected function getName(): string
    {
        if ($this->code == 'C') {
            return __('Clubs');
        } elseif ($this->code == 'D') {
            return __('Diamonds');
        } elseif ($this->code == 'H') {
            return __('Hearts');
        } else {
            return __('Spades');
        }
    }

    public function __toString()
    {
        return $this->getCode();
    }

    public static function all(): Collection
    {
        return collect(self::SUITS);
    }
}
