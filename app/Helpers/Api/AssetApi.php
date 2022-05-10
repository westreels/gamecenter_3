<?php

namespace App\Helpers\Api;

use App\Models\Asset;
use Illuminate\Support\Collection;

abstract class AssetApi extends Api
{
    const TYPE_LIST = 'list';
    const TYPE_PRICE = 'price';
    const TYPE_HISTORY = 'history';

    protected $assetIdAttribute = 'external_id';

    abstract protected function getEndpoints(): array;

    abstract protected function getKeys(): array;

    abstract protected function getMappers(): array;

    protected function getEndpoint(string $type, ...$params): string
    {
        return $params ? sprintf($this->getEndpoints()[$type], ...$params) : $this->getEndpoints()[$type];
    }

    protected function getDataKey(string $type): string
    {
        return $this->getKeys()[$type];
    }

    protected function getData(string $type, ...$params)
    {
        $endpoint = $this->getEndpoint($type, ...$params);
        $key = $this->getDataKey($type);
        $data = $this->get($endpoint);

        if (is_object($data) && $key) {
            $data = data_get($data, $key);
        }

        $data = is_array($data)
            ? collect($data)
            : (is_numeric($data) ? (float) $data : $data);

        $mapperFunction = $this->getMappers()[$type] ?? NULL;

        return $mapperFunction && $data ? $mapperFunction($data) : $data;
    }

    public function getList(): ?Collection
    {
        return $this->getData(self::TYPE_LIST);
    }

    public function getPrice(Asset $asset): ?float
    {
        return $this->getData(self::TYPE_PRICE, $asset->{$this->assetIdAttribute});
    }

    public function getHistory(Asset $asset, ...$params): ?Collection
    {
        return $this->getData(self::TYPE_HISTORY, $asset->{$this->assetIdAttribute}, ...$params);
    }
}
