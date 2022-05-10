<?php

namespace App\Console\Commands;

use App\Facades\ChatMessage;
use App\Models\Bonus;
use App\Models\ChatRoom;
use App\Models\User;
use App\Services\AccountService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MakeRain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rain:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make credits rain';

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
        $frequency = config('settings.bonuses.rain.frequency');
        $amounts = config('settings.bonuses.rain.amounts');
        $chatUserId = config('settings.bonuses.rain.user');

        if (config('settings.interface.chat.enabled') && $frequency && count($amounts) > 0 && $chatUserId) {
            $startTime = Carbon::now();

            if ($frequency == 'everyFifteenMinutes') {
                $startTime->subMinutes(15);
            } elseif ($frequency == 'everyThirtyMinutes') {
                $startTime->subMinutes(30);
            } elseif ($frequency == 'hourly') {
                $startTime->subHour();
            } elseif ($frequency == 'everyTwoHours') {
                $startTime->subHours(2);
            } elseif ($frequency == 'everyThreeHours') {
                $startTime->subHours(3);
            } elseif ($frequency == 'everyFourHours') {
                $startTime->subHours(4);
            } elseif ($frequency == 'everySixHours') {
                $startTime->subHours(6);
            } elseif ($frequency == 'daily') {
                $startTime->subDay();
            } elseif ($frequency == 'weekly') {
                $startTime->subWeek();
            } elseif ($frequency == 'monthly') {
                $startTime->subMonth();
            }

            $users = User::regular()
                ->select('users.id', 'users.name', DB::raw('LENGTH(REPLACE(GROUP_CONCAT(chat_messages.message SEPARATOR ""), " ", "")) AS messages_length'))
                ->join('chat_messages', function ($query) use ($startTime) {
                    $query->on('chat_messages.user_id', '=', 'users.id');
                    $query->where('chat_messages.created_at', '>=', $startTime);
                })
                ->with('account')
                ->groupBy('users.id', 'users.name')
                ->orderBy('messages_length', 'desc')
                ->take(count($amounts))
                ->get();

            info(sprintf('Making rain to users: %s', $users->isNotEmpty() ? $users->pluck('id') : 'none'));

            if ($users->isNotEmpty()) {
                $users->each(function ($user, $i) use ($amounts) {
                    (new AccountService($user->account))->createBonus(
                        Bonus::TYPE_RAIN,
                        $amounts[$i]
                    );
                });

                ChatRoom::enabled()->get()->each(function (ChatRoom $room) use ($users, $chatUserId, $amounts) {
                    ChatMessage::send(
                        $room,
                        User::find($chatUserId),
                        __('Credit rain for the following users')
                            . ': '
                            . $users->map(function ($user, $i) use ($amounts) {
                                return $user->name . ' (' . $amounts[$i] . __('credits') . ')';
                            })->implode(', '),
                        $users->pluck('id')->toArray()
                    );
                });
            }
        }

        return 0;
    }
}
