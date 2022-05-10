<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Helpers\Games;

use App\Helpers\MagicGetters;
use Illuminate\Support\Collection;

class CardValue
{
    use MagicGetters;

    protected const VALUES = [
        '2', '3', '4', '5', '6', '7', '8', '9', 'T', 'J', 'Q', 'K', 'A'
    ];

    protected $code;

    public function __construct(string $code)
    {
        if (!in_array($code, self::VALUES, TRUE)) {
            throw new \Exception(sprintf('Value "%s" is not supported.', $code));
        }

        $this->code = $code;

        return $this;
    }

    protected function getRank(): int
    {
        return array_search($this->code, self::VALUES, TRUE);
    }

    protected function getCode(): string
    {
        return $this->code;
    }

    protected function getIsTen(): bool
    {
        return $this->code == 'T';
    }

    protected function getIsJack(): bool
    {
        return $this->code == 'J';
    }

    protected function getIsQueen(): bool
    {
        return $this->code == 'Q';
    }

    protected function getIsKing(): bool
    {
        return $this->code == 'K';
    }

    protected function getIsAce(): bool
    {
        return $this->code == 'A';
    }

    protected function getIsTwo(): bool
    {
        return $this->code == '2';
    }

    protected function getName(): string
    {
        if ($this->code == '2') {
            return __('Two');
        } elseif ($this->code == '3') {
            return __('Three');
        } elseif ($this->code == '4') {
            return __('Four');
        } elseif ($this->code == '5') {
            return __('Five');
        } elseif ($this->code == '6') {
            return __('Six');
        } elseif ($this->code == '7') {
            return __('Seven');
        } elseif ($this->code == '8') {
            return __('Eight');
        } elseif ($this->code == '9') {
            return __('Nine');
        } elseif ($this->code == 'T') {
            return __('Ten');
        } elseif ($this->code == 'J') {
            return __('Jack');
        } elseif ($this->code == 'Q') {
            return __('Queen');
        } elseif ($this->code == 'K') {
            return __('King');
        } else {
            return __('Ace');
        }
    }

    public function __toString()
    {
        return $this->getCode();
    }

    public static function all(): Collection
    {
        return collect(self::VALUES);
    }
}
