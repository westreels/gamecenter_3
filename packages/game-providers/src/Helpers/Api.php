<?php

namespace Packages\GameProviders\Helpers;

use App\Helpers\Api\Api as ParentApi;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class Api extends ParentApi
{
    protected $config;

    protected $provider;

    public function __construct()
    {
        $this->provider = Str::of(class_basename(get_called_class()))->lower()->replace('api', '');
        $this->config = config('game-providers.providers.' . $this->provider);

        parent::__construct();
    }

    public function config(string $key)
    {
        return data_get($this->config, $key);
    }

    public function getUnauthorizedResponseData(): array
    {
        return [];
    }

    public function getGame($id): ?array
    {
        return $this->getGamesList()->get($id);
    }

    public function getGameName($id): ?string
    {
        return $this->getGame($id)['name'] ?? NULL;
    }

    abstract public function isEnabled(): bool;

    abstract public function convertCreditsToCurrency(float $amount);

    abstract public function convertCurrencyToCredits(float $amount);

    abstract public function getGamesList(): Collection;

    abstract public function getGameUrl(string $gameId, User $user, Request $request): ?string;

    abstract public function callbackIsAuthorized(Request $request): bool;

    public static function make(string $provider): Api
    {
        $class = sprintf('Packages\\GameProviders\\Helpers\\%sApi', Str::ucfirst($provider));

        if (!class_exists($class)) {
            throw new Exception(sprintf('Game provider %s does not exist.', $provider));
        }

        return app()->make($class);
    }
}
