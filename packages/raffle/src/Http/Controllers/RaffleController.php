<?php

namespace Packages\Raffle\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Packages\Raffle\Helpers\Queries\RaffleQuery;
use Packages\Raffle\Http\Requests\BuyRaffleTicket;
use Packages\Raffle\Models\Raffle;
use Packages\Raffle\Services\RaffleService;

class RaffleController extends Controller
{
    public function index(Request $request, RaffleQuery $query)
    {
        return $request->has('completed')
            ? $this->completed($request, $query)
            : $this->current($request, $query);
    }

    /**
     * Get raffles for the front page
     *
     * @return mixed
     */
    public function featured()
    {
        return Raffle::inProgress()
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map
            ->only(['id', 'title', 'banner', 'ticket_price', 'total_tickets', 'max_pot_size']);
    }

    protected function current(Request $request, RaffleQuery $query)
    {
        $account = $request->user()->account;

        $items = $query
            // where clause should be applied before getBuilder()
            ->addWhere(['status', '=', Raffle::STATUS_IN_PROGRESS])
            ->getBuilder()
            // load tickets_count
            ->withCount(['tickets', 'tickets AS user_tickets_count' => function (Builder $query) use ($account) {
                $query->where('account_id', $account->id);
            }])
            ->orderBy('id', 'desc')
            ->get()
            ->map
            // hide tickets relationship
            ->setHidden(['tickets']);

        return $items;
    }

    protected function completed(Request $request, RaffleQuery $query)
    {
        $items = $query
            // where clause should be applied before getBuilder()
            ->addWhere(['status', '=', Raffle::STATUS_COMPLETED])
            ->getBuilder()
            ->with('winningTicket.account.user')
            ->withCount('tickets')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map
            // hide tickets relationship
            ->setHidden(['tickets']);
        ;

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function buy(BuyRaffleTicket $request, Raffle $raffle)
    {
        $raffleService = new RaffleService($raffle);
        $raffleService->buy($request->quantity);

        $account = $request->user()->account;

        return [
            'raffle' => $raffle
                ->refresh()
                // load tickets_count
                ->loadCount(['tickets', 'tickets AS user_tickets_count' => function (Builder $query) use ($account) {
                    $query->where('account_id', $account->id);
                }])
                ->setHidden(['tickets']), // but hide tickets relationship
            'account' => $request->user()->account
        ];
    }
}
