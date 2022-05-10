<?php

namespace Packages\GameProviders\Helpers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

class BgamingApi extends Api
{
    const SIGNATURE_HEADER = 'X-REQUEST-SIGN';

    protected function getBaseUrl(): string
    {
        return $this->config('api.endpoint');
    }

    public function getUnauthorizedResponseData(): array
    {
        return [
            'code' => 403,
            'message' => 'Forbidden'
        ];
    }

    public function isEnabled(): bool
    {
        return $this->config('api.id') && $this->config('api.secret_key');
    }

    public function convertCreditsToCurrency(float $amount)
    {
        $rate = 1 / $this->config('currency.rate');
        $n = pow(10, $this->config('currency.decimals'));
        return floor($amount * $n * $rate);
    }

    public function convertCurrencyToCredits(float $amount)
    {
        $rate = $this->config('currency.rate');
        $n = pow(10, -$this->config('currency.decimals'));
        return $amount * $n * $rate;
    }

    public function getGamesList(): Collection
    {
        return Cache::remember('game-providers.bgaming.games-list', 600, function () {
            return collect(Yaml::parse(File::get(base_path('packages/game-providers/config/bgaming-games.yml'))))
                ->mapWithKeys(function ($game) {
                    return [
                        $game['identifier'] => [
                            'id' => $game['identifier'],
                            'name' => $game['title'],
                            // s1 - 236x110
                            // s2 - 337x181
                            // s3 - 380x380
                            // s4 - 190x190
                            'banner' => sprintf('https://cdn.softswiss.net/i/s3/softswiss/%s.png', $game['identifier']),
                            'category' => Str::of($game['category'])->replace('_', ' ')->ucfirst(),
                            'provider' => Str::ucfirst($this->provider),
                        ]
                    ];
                })
                ;
        });
    }

    public function getGameUrl(string $gameId, User $user, Request $request): ?string
    {
        $params = [
            'casino_id' => $this->config('api.id'),
            'game' => $gameId,
            'currency' => $this->config('currency.code'),
            'locale' => app()->getLocale(),
            'ip' => $request->ip(),
            'balance' => $this->convertCreditsToCurrency($user->account->balance),
            'client_type' => $request->query('is_mobile') ? 'mobile' :'desktop',
            'urls' => [
                'return_url' => url('/'),
                'deposit_url' => url('/user/account/deposits'),
            ],
            'user' => [
                'id' => $user->code,
                'email' => $user->email,
                'firstname' => $user->name,
                'registered_at' => $user->created_at->format('Y-m-d')
            ]
        ];

        return $this->post('sessions', 'launch_options.game_url', [
            'headers' => [
                self::SIGNATURE_HEADER => $this->getSignature($params)
            ],
            'form_params' => $params
        ]);
    }

    public function callbackIsAuthorized(Request $request): bool
    {
        return $this->signatureIsValid($request);
    }

    protected function signatureIsValid(Request $request): bool
    {
        return $this->getSignature($request->getContent()) == $request->header(self::SIGNATURE_HEADER);
    }

    protected function getSignature($params): string
    {
        return hash_hmac('sha256', is_array($params) ? json_encode($params) : $params, $this->config('api.secret_key'));
    }
}
