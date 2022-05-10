<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace Packages\GameProviders\Services;

use App\Events\GamePlayed;
use App\Facades\AccountTransaction;
use App\Models\Game;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Packages\GameProviders\Helpers\EvoplayApi;
use Packages\GameProviders\Models\EvoplayGame;

class EvoplayCallbackService extends CallbackService
{
    protected $gameableClass = EvoplayGame::class;

    protected $apiClass = EvoplayApi::class;

    protected $displayErrorToUser = FALSE;

    protected function getUserId(): ?string
    {
        return $this->getValue('token');
    }

    protected function getExternalGameId(): ?string
    {
        return $this->getValue('name') == 'refund'
            ? $this->getValue('data.refund_round_id')
            : $this->getValue('data.round_id');
    }

    protected function parseData()
    {
        $data = $this->request->all();
        data_set($data, 'data.details', json_decode(data_get($data, 'data.details'))); // Convert JSON string --> array
        return $data;
    }

    protected function validateRequest()
    {
        if (!$this->user) {
            $this->errorMessage = sprintf('Unknown user %s', $this->getUserId());
        } elseif (!$this->user->is_active) {
            $this->errorMessage = sprintf('User %s is blocked', $this->getUserId());
        }

        if ($this->getValue('name') == 'bet' && $this->api->convertCreditsToCurrency($this->balance) < (float) $this->getValue('data.amount')) {
            $this->displayErrorToUser = TRUE;
            $this->errorMessage = 'Insufficient balance';
        }
    }

    protected function play(): CallbackService
    {
        $action = 'action' . Str::ucfirst($this->getValue('name')); // init | bet | win | refund

        if (method_exists($this, $action) && $this->getExternalGameId()) {
            $this->$action();
        }

        return $this;
    }

    protected function createGameable(): Model
    {
        $gameable = new $this->gameableClass();
        $gameable->game_id = $this->getValue('data.details.game.game_id');
        $gameable->name = $this->api->getGameName($gameable->game_id);
        $gameable->external_id = $this->getExternalGameId();
        $gameable->data = collect([$this->getValue('callback_id') => $this->getValue('data')]);
        $gameable->save();

        return $gameable;
    }

    protected function actionBet(): CallbackService
    {
        DB::transaction(function () {
            $gameable = $this->loadGameable();  //get db game-evo
            $bet = $this->api->convertCurrencyToCredits($this->getValue('data.amount'));

            if (!$gameable) {
                $gameable = $this->createGameable();

                $game = new Game();
                $game->account()->associate($this->user->account);
                $game->provablyFairGame()->associate($this->createProvablyFairGame());
                $game->bet = $bet;

                $gameable->game()->save($game);
                AccountTransaction::create($this->user, $gameable->game, -$bet, FALSE);
            } else {
                $data = $gameable->data;
                $callbackId = $this->getValue('callback_id');

                // if this callback is not yet processed
                if (!$data->get($callbackId)) {
                    $data->put($callbackId, $this->getValue('data'));
                    $gameable->data = $data;
                    $gameable->game->bet += $bet;
                    $gameable->save();
                    $gameable->game->save();

                    AccountTransaction::create($this->user, $gameable->game, -$bet, FALSE);
                }
            }
        });

        return $this;
    }

    protected function actionWin(): CallbackService
    {
        DB::transaction(function () {
            $gameable = $this->loadGameable();

            if ($gameable) {
                $game = $gameable->game;
                $data = $gameable->data;
                $callbackId = $this->getValue('callback_id');

                // if this callback is not yet processed
                if (!$data->get($callbackId)) {
                    $data->put($callbackId, $this->getValue('data'));
                    $gameable->data = $data;

                    $win = $this->api->convertCurrencyToCredits($this->getValue('data.amount'));
                    if ($win > 0) {
                        $game->win += $win;

                        AccountTransaction::create($this->user, $game, $win, FALSE);
                    }

                    // mark game as completed
                    if ($this->getValue('data.details.final_action') == 1) {
                        $game->is_completed = TRUE;
                    }
                    
                    $gameable->save();
                    $game->save();

                    // throw new GamePlayed event
                    if ($game->is_completed) {
                        event(new GamePlayed($game, $this->user)); //change
                    }
                }
            } else {
                Log::error(sprintf('Evoplay game can not be found: %d', $this->getValue('data.round_id')));
            }
        });

        return $this;
    }

    protected function actionRefund(): CallbackService
    {
        DB::transaction(function () {
            $gameable = $this->loadGameable();

            if ($gameable) {
                $game = $gameable->game;
                $data = $gameable->data;
                $callbackId = $this->getValue('callback_id');
                $refundCallbackId = $this->getValue('data.refund_callback_id');

                // process a refund
                if ($data->get($refundCallbackId) && $data->where('refund_callback_id', $refundCallbackId)->count() == 0) {
                    $data->put($callbackId, $this->getValue('data'));
                    $gameable->data = $data;
                    $gameable->save();

                    $refundAmount = $this->api->convertCurrencyToCredits($this->getValue('data.amount'));
                    AccountTransaction::create($this->user, $game, $refundAmount, FALSE);
                }
            }
        });

        return $this;
    }

    public function getSuccessResponse(): array
    {
        $client = new Client();
        
        $url = config('define.balance_dev.domain') . '/balance-game/' . $this->user->social_id;

        $res = $client->request('GET', $url);

        $body = $res->getBody()->getContents() ?? null;

        $body = isset($body) ? json_decode($body, true) : [];

        // $this->balance = $body['data']['data']['balance'];
        return [
            'status' => 'ok',
            'data' => [
                'balance' => $this->api->convertCreditsToCurrency($body['data']['data']['balance']), //$this->user->account->balance
                'currency' => $this->api->config('currency.code')
            ]
        ];
    }

    public function getErrorResponse(): array
    {
        return [
            'status' => 'error',
            'error' => [
                'scope' => $this->displayErrorToUser ? 'user' : 'internal', // internal - error message is not available to user, user - error message seen by user
                'no_refund' => 1, // Indicates whether the callback can or cannot be resent: 1 - callback should not be resent, 0 - callback can be resent
                'message' => $this->errorMessage
            ]
        ];
    }
}
