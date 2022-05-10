<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DeleteBots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:delete {count=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete bots';

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
        User::bots()
            ->orderBy('id', 'desc')
            ->limit(intval($this->argument('count')))
            ->get()
            // it's important to load each model one by one,
            // otherwise model's delete() method would not be fired and polymorphic relations would not be deleted.
            ->each(function ($user) {
                $user->delete();
            });

        return 0;
    }
}
