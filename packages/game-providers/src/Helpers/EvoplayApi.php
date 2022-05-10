<?php

namespace Packages\GameProviders\Helpers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class EvoplayApi extends Api
{
    protected $errorMessageKey = 'error.message';

    protected function getBaseUrl(): string
    {
        return $this->config('api.endpoint');
    }

    public function isEnabled(): bool
    {
        return $this->config('api.id') && $this->config('api.secret_key');
    }

    public function convertCreditsToCurrency(float $amount)
    {
        $rate = 1 / $this->config('currency.rate');
        return round(floor($amount) * $rate, $this->config('currency.decimals'));
    }

    public function convertCurrencyToCredits(float $amount)
    {
        $rate = $this->config('currency.rate');
        return $amount * $rate;
    }

    public function getGamesList(): Collection
    {
        $requestParams = ['need_extra_data' => 0];

        return Cache::remember('game-providers.evoplay.games-list', 600, function () use ($requestParams) {
            return collect($this->get('Game/getList', 'data', $this->getQuery($requestParams)))
                ->map(function ($game, $gameId) {
                    return [
                        'id' => '' . $gameId,
                        'name' => $game->name,
                        'banner' => asset(sprintf('images/game-providers/evoplay/games/%s_360x360.jpg', Str::afterLast($game->absolute_name, '\\'))),
                        'category' => Str::ucfirst($game->game_sub_type),
                        'provider' => Str::ucfirst($this->provider)
                    ];
                });
        });
    }

    public function getGameUrl(string $gameId, User $user, Request $request): ?string
    {
        $requestParams = [
            'token' => $user->code,
            'game' => $gameId,
            'settings' => [
                'user_id' => $user->id,
                'exit_url' => url('/'),
                'cash_url' => url('/user/account/deposits'),
                'language' => app()->getLocale(),
                'https' => $request->isSecure() ? 1 : 0
            ],
            'denomination' => 'default',
            'currency' => $this->config('currency.code'),
            'return_url_info' => 1,
            'callback_version' => $this->config('api.callback_version'),
        ];
        return $this->get('Game/getURL', 'data.link', $this->getQuery($requestParams));
    }

    protected function getQuery(array $requestParams = []): array
    {
        return [
            'query' => array_merge($this->getSignatureRequestParams($requestParams), $requestParams)
        ];
    }

    protected function getSignatureRequestParams(array $requestParams = [])
    {
        return [
            'project' => $this->config('api.id'),
            'version' => $this->config('api.version'),
            'signature' => $this->getRequestSignature($requestParams)
        ];
    }

    public function callbackIsAuthorized(Request $request): bool
    {
        return $this->remoteIpValid($request) && $this->signatureIsValid($request);
    }

    protected function remoteIpValid(Request $request): bool
    {
        $allowedIps = $this->config('api.allowed_callback_ips');
        return is_array($allowedIps) && in_array($request->ip(), $allowedIps, TRUE);
    }

    protected function signatureIsValid(Request $request): bool
    {
        return $this->getCallbackSignature($request->except('signature')) == $request->signature;
    }

    protected function getRequestSignature(array $params = [])
    {
        return $this->getSignature($this->config('api.version'), $params);
    }

    protected function getCallbackSignature(array $params = [])
    {
        return $this->getSignature($this->config('api.callback_version'), $params);
    }

    protected function getSignature(int $version, array $params = [])
    {
        $signatureComponents = [$this->config('api.id'), $version];

        foreach ($params as $param) {
            if(is_array($param)){
                if(count($param)) {
                    $arrayParams = [];
                    array_walk_recursive($param, function($item) use (&$arrayParams) {
                        if(!is_array($item)) {
                            $arrayParams[] = $item;
                        }
                    });
                    $signatureComponents[] = implode(':', $arrayParams);
                } else {
                    $signatureComponents[] = '';
                }
            } else {
                $signatureComponents[] = $param;
            }
        };

        $signatureComponents[] = $this->config('api.secret_key');

        return md5(implode('*', $signatureComponents));
    }
}
