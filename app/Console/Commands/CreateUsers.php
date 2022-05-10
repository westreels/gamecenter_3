<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\UserService;
use Faker\Factory as Faker;
use Illuminate\Auth\Events\Registered;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {count=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create bots';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // init Faker
        $faker = Faker::create();

        for ($i=0; $i < intval($this->argument('count')); $i++) {
            $user = UserService::create([
                'name'      => $faker->name,
                'email'     => $faker->safeEmail,
                'password'  => Str::random(10),
                'role'      => User::ROLE_BOT
            ]);

            // throw Registered event
            event(new Registered($user));
        }

        return 0;
    }
}
