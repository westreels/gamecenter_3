<?php

namespace App\Services;


class OAuthService
{
    private $providers;

    public function __construct()
    {
        // filter only service providers, which have specific attributes
        $this->providers = config('oauth');
    }

    /**
     * Get all providers
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->providers;
    }

    /**
     * Get only enabled providers
     *
     * @return array
     */
    public function getEnabled(array $properties = []): array
    {
        $enabledProviders = array_filter($this->providers, function ($provider) {
            return $provider['client_id'] ?? FALSE
                && $provider['client_secret'] ?? FALSE
                && $provider['redirect'] ?? FALSE;
        });

        return empty($properties)
            ? $enabledProviders
            : array_map(function ($provider) use ($properties) {
                foreach ($provider as $property => $value) {
                    if (!in_array($property, $properties)) {
                        unset($provider[$property]);
                    }
                }
                return $provider;
            }, $enabledProviders);
    }
}
