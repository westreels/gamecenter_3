<?php

namespace Packages\Raffle\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Packages\Raffle\Helpers\Queries\RaffleQuery;
use Packages\Raffle\Helpers\Queries\RaffleTicketQuery;
use Packages\Raffle\Http\Requests\Admin\CompleteRaffle;
use Packages\Raffle\Http\Requests\Admin\CreateRaffle;
use Packages\Raffle\Http\Requests\Admin\UpdateRaffle;
use Packages\Raffle\Models\Raffle;
use Packages\Raffle\Models\RaffleTicket;
use Packages\Raffle\Services\RaffleService;

class RaffleController extends Controller
{
    public function index(RaffleQuery $query)
    {
        $items = $query
            ->getBuilder()
            ->with('winningTicket.account.user')
            ->withCount('tickets')
            ->get()
            ->map(function ($raffle) {
                $raffle->append('fee_amount');

                if ($raffle->winningTicket) {
                    $raffle->winningTicket->account->user->append(
                        'two_factor_auth_enabled',
                        'two_factor_auth_passed',
                        'is_admin',
                        'is_bot',
                        'is_active'
                    );
                }

                return $raffle;
            });

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function show(Raffle $raffle)
    {
        return $raffle;
    }

    public function store(CreateRaffle $request)
    {
        $raffle = new Raffle();
        $raffle->title = $request->title;
        $raffle->description = $request->description;
        $raffle->banner = $request->banner;
        $raffle->ticket_price = $request->ticket_price;
        $raffle->max_tickets_per_user = $request->max_tickets_per_user;
        $raffle->total_tickets = $request->total_tickets;
        $raffle->fee = $request->fee;
        $raffle->completion_trigger = $request->completion_trigger;
        $raffle->start_date = Carbon::now();
        $raffle->recurring = $request->recurring;

        if ($raffle->completion_trigger == Raffle::TRIGGER_TIME) {
            $raffle->end_date = $raffle->start_date->addSeconds($request->duration);
        }

        return $raffle->save();
    }

    public function update(UpdateRaffle $request, Raffle $raffle)
    {
        $raffle->title = $request->title;
        $raffle->description = $request->description;
        $raffle->banner = $request->banner;
        $raffle->ticket_price = $request->ticket_price;
        $raffle->max_tickets_per_user = $request->max_tickets_per_user;
        $raffle->total_tickets = $request->total_tickets;
        $raffle->fee = $request->fee;
        $raffle->recurring = $request->recurring;

        return $raffle->save();
    }

    public function complete(Raffle $raffle)
    {
        RaffleService::complete($raffle);

        return TRUE;
    }

    public function tickets(Raffle $raffle, RaffleTicketQuery $query)
    {
        $items = $query->addWhere(['raffle_id', '=', $raffle->id])
            ->getBuilder()
            ->with('account.user')
            ->get()
            ->map(function ($ticket) {
                $ticket->account->user->append(
                    'two_factor_auth_enabled',
                    'two_factor_auth_passed',
                    'is_admin',
                    'is_bot',
                    'is_active'
                );

                return $ticket;
            });

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }
}
