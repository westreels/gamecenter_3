<?php

namespace Database\Seeders;

use App\Models\Provider;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    protected $providers = [
        [
            'code' => 'bgaming',
            'name' => 'BGaming',
            'url' => 'https://www.bgaming.com'
        ],
        [
            'code' => 'evoplay',
            'name' => 'Evoplay',
            'url' => 'https://evoplay.games'
        ]
    ];

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        collect($this->providers)->each(function ($provider) {
            $provider = collect($provider);

            Provider::firstOrCreate(
                $provider->only('code')->toArray(),
                $provider->only('name', 'url')->toArray()
            );
        });
    }
}
