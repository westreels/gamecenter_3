<?php

namespace Packages\Raffle\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Packages\Raffle\Models\Raffle;

class TicketsLimit implements Rule
{
    protected $raffle;
    protected $user;
    protected $limit;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Raffle $raffle, User $user)
    {
        $this->raffle = $raffle;
        $this->user = $user;
    }

    public function calculate()
    {
        $maxTicketsPerUser = $this->raffle->max_tickets_per_user  ?: $this->raffle->total_tickets;

        $this->limit = min(
            // number of tickets left in the raffle
            $this->raffle->total_tickets - $this->raffle->tickets->count(),
            // number of tickets allowed for a user
            $maxTicketsPerUser - $this->raffle->tickets->where('account_id', $this->user->account->id)->count()
        );

        return $this;
    }

    public function get()
    {
        return $this->limit;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->calculate();

        return $value <= $this->limit;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('You can not buy more than :n ticket(s).', ['n' => $this->limit]);
    }
}
