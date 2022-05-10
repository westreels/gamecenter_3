<?php

namespace Packages\Payments\Helpers;

class Blockchain
{
    public static function getAddressModelClassById(string $id): string
    {
        return sprintf('Packages\\Payments\\Models\\%sAddress', ucfirst($id));
    }

    public static function getStoreAddressRequestClassById(string $id): string
    {
        return sprintf('Packages\\Payments\\Http\\Requests\\Store%sAddress', ucfirst($id));
    }
}
