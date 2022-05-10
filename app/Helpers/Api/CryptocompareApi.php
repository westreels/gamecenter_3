<?php

namespace App\Helpers\Api;

use Illuminate\Support\Collection;

class CryptocompareApi extends CryptoApi
{
    protected $assetIdAttribute = 'symbol';

    protected function getBaseUrl(): string
    {
        return config('services.api.crypto.providers.cryptocompare.endpoint');
    }

    protected function getEndpoints(): array
    {
        $apiKey = config('services.api.crypto.providers.cryptocompare.api_key');

        return [
            parent::TYPE_LIST      => 'top/mktcapfull?limit=100&tsym=USD&api_key=' . $apiKey,
            parent::TYPE_PRICE     => 'price?fsym=%s&tsyms=USD&api_key=' . $apiKey,
            parent::TYPE_HISTORY   => 'v2/histominute?fsym=%s&tsym=USD&limit=60&api_key=' . $apiKey,
        ];
    }

    protected function getKeys(): array
    {
        return [
            parent::TYPE_LIST      => 'Data',
            parent::TYPE_PRICE     => 'USD',
            parent::TYPE_HISTORY   => 'Data.Data',
        ];
    }

    protected function getMappers(): array
    {
        return [
            parent::TYPE_LIST => function (Collection $data) {
                return $data->map(function ($item, $i) {
                    return (object) [
                        'id'        => $item->CoinInfo->Id,
                        'symbol'    => $item->CoinInfo->Name,
                        'name'      => $item->CoinInfo->FullName,
                        'price'     => (float) data_get($item, 'RAW.USD.PRICE', 0),
                        'rank'      => $i+1
                    ];
                });
            },
            parent::TYPE_HISTORY => function (Collection $data) {
                return $data->map(function ($item) {
                    return (object) [
                        'date'  => $item->time * 1000,
                        'value' => $item->close
                    ];
                });
            }
        ];
    }
}
