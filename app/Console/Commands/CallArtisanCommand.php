<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CallArtisanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:call {name=cache:clear}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call artisan command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Artisan::call($this->argument('name'));

        return 0;
    }
}
