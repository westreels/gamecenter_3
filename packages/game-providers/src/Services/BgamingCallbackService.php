<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace Packages\GameProviders\Services;

use App\Events\GamePlayed;
use App\Facades\AccountTransaction;
use App\Models\Game;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Packages\GameProviders\Helpers\BgamingApi;
use Packages\GameProviders\Models\BgamingGame;

class BgamingCallbackService extends CallbackService
{
    protected $gameableClass = BgamingGame::class;

    protected $apiClass = BgamingApi::class;

    protected function getUserId(): ?string
    {
        return $this->getValue('user_id');
    }

    protected function getExternalGameId(): ?string
    {
        return $this->getValue('game_id');
    }

    protected function parseData()
    {
        return $this->request->all();
    }

    protected function validateRequest()
    {
        if (!$this->user) {
            $this->errorCode = 101;
            $this->errorMessage = sprintf('Unknown user %s', $this->getUserId());
        } elseif (!$this->user->is_active) {
            $this->errorCode = 110;
            $this->errorMessage = sprintf('User %s is blocked', $this->getUserId());
        }

        $betAmount = collect($this->getValue('actions'))->where('action', 'bet')->sum('amount');
        if ($this->api->convertCreditsToCurrency($this->user->account->balance) < $betAmount) {
            $this->httpCode = 412;
            $this->errorCode = 100;
            $this->errorMessage = sprintf('Insufficient balance %.2f for user %s', $this->user->account->balance, $this->getUserId());
        }
    }

    protected function play(): CallbackService
    {
        DB::transaction(function () {
            $gameable = $this->loadGameable();
            $actions = collect($this->getValue('actions'));

            // if some actions are passed
            // note, that there can be no actions passed when game is initialized or when the "finished" attribute set to TRUE
            if ($actions->isNotEmpty()) {
                // if game was not yet created
                if (!$gameable) {
                    $gameable = $this->createGameable();

                    $game = new Game();
                    $game->account()->associate($this->user->account);
                    $game->provablyFairGame()->associate($this->createProvablyFairGame());
                    $game->bet = 0;

                    $gameable->game()->save($game);
                }

                $data = $gameable->data;
                // remove already processed actions
                $actions = $actions->filter(function ($action) use ($data) {
                    return !$data->containsStrict('action_id', $action['action_id']);
                });
                // save actions data
                $gameable->data = $data->concat($actions);

                // loop through actions
                $actions->each(function ($action) use ($gameable) {
                    if ($action['action'] == 'bet') {
                        $amount = $this->api->convertCurrencyToCredits($action['amount']);
                        $gameable->game->bet += $amount;
                        AccountTransaction::create($this->user->account, $gameable->game, -$amount, FALSE);
                    } elseif ($action['action'] == 'win') {
                        $amount = $this->api->convertCurrencyToCredits($action['amount']);
                        $gameable->game->win += $amount;
                        AccountTransaction::create($this->user->account, $gameable->game, $amount, FALSE);
                    } elseif ($action['action'] == 'rollback') {
                        $originalAction = $gameable->data->where('action_id', $action['original_action_id'])->first();
                        if ($originalAction && in_array($originalAction['action'], ['bet', 'win'])) {
                            AccountTransaction::create(
                                $this->user->account,
                                $gameable->game,
                                $originalAction['action'] == 'bet' ? $originalAction['amount'] : -$originalAction['amount'],
                                FALSE
                            );
                        }
                    }
                });
            }

            // make sure gameable model is loaded or created
            if ($gameable) {
                if ($this->getValue('finished')) {
                    $gameable->game->is_completed = TRUE;
                }

                $gameable->save();
                $gameable->game->save();

                // throw new GamePlayed event
                if ($gameable->game->is_completed) {
                    event(new GamePlayed($gameable->game));
                }
            }
        });

        return $this;
    }

    protected function createGameable(): Model
    {
        $gameable = new $this->gameableClass();
        $gameable->game_id = $this->getValue('game');
        $gameable->name = $this->api->getGameName($gameable->game_id);
        $gameable->external_id = $this->getExternalGameId();
        $gameable->data = collect();
        $gameable->save();

        return $gameable;
    }

    public function getSuccessResponse(): array
    {
        $result = ['balance' => $this->api->convertCreditsToCurrency($this->user->account->balance)];

        if ($this->getExternalGameId()) {
            $result['game_id'] = $this->getExternalGameId();
            $result['transactions'] = collect($this->getValue('actions'))->map(function ($tx) {
                return [
                    'action_id'     => $tx['action_id'],
                    'tx_id'         => $tx['action_id'],
                    'processed_at'  => Carbon::now()->toIso8601String(),
                ];
            });
        }

        return $result;
    }

    public function getErrorResponse(): array
    {
        return [
            'code' => $this->errorCode,
            'message' => $this->errorMessage
        ];
    }
}
