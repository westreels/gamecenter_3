<?php

namespace App\Helpers\Api;

use Illuminate\Support\Collection;

class CoincapApi extends CryptoApi
{
    protected function getBaseUrl(): string
    {
        return config('services.api.crypto.providers.coincap.endpoint');
    }

    protected function getEndpoints(): array
    {
        return [
            parent::TYPE_LIST      => 'assets?limit=2000',
            parent::TYPE_PRICE     => 'assets/%s',
            parent::TYPE_HISTORY   => 'assets/%s/history?interval=m1&start=%s&end=%s',
        ];
    }

    protected function getKeys(): array
    {
        return [
            parent::TYPE_LIST      => 'data',
            parent::TYPE_PRICE     => 'data.priceUsd',
            parent::TYPE_HISTORY   => 'data',
        ];
    }

    protected function getMappers(): array
    {
        return [
            parent::TYPE_LIST => function (Collection $data) {
                return $data->map(function ($item, $i) {
                    return (object) [
                        'id'        => $item->id,
                        'symbol'    => $item->symbol,
                        'name'      => $item->name,
                        'price'     => (float) $item->priceUsd,
                        'rank'      => (int) $item->rank
                    ];
                });
            },
            parent::TYPE_HISTORY => function (Collection $data) {
                return $data->map(function ($item) {
                    return (object) [
                        'date'  => $item->time,
                        'value' => (float) $item->priceUsd
                    ];
                });
            }
        ];
    }
}
