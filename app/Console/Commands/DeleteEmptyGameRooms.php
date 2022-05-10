<?php

namespace App\Console\Commands;

use App\Models\GameRoom;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class DeleteEmptyGameRooms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game-room:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete empty game rooms';

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
        // delete game rooms that have no players and not updated for more than 1 day
        // or updated more than 3 days ago
        GameRoom::where(function ($query) {
                $query->doesntHave('players')->where('updated_at', '<', Carbon::now()->subDay());
            })
            ->orWhere('updated_at', '<', Carbon::now()->subDays(3))
            ->delete();

        return 0;
    }
}
