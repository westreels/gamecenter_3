<?php

namespace App\Console\Commands;

use App\Models\ProvablyFairGame;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class DeleteUnusedProvablyFairGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:delete-unused';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete unused provably fair games';

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
        // delete provably fair games that don't have game relation (entry in games table)
        ProvablyFairGame::where('created_at', '<', Carbon::now()->subDay())
            ->doesntHave('game')
            ->delete();

        return 0;
    }
}
