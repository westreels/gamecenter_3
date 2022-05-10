<?php

namespace Packages\GameProviders\Services;

use App\Models\Account;
use App\Models\ProvablyFairGame;
use App\Models\User;
use Closure;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Packages\GameProviders\Helpers\Api;

abstract class CallbackService
{
    /**
     * @var Model
     */
    protected $gameableClass;

    /**
     * @var string
     */
    protected $apiClass;

    /**
     * @var Api
     */
    protected $api;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var int
     */
    protected $httpCode = 200;

    /**
     * @var int
     */
    protected $errorCode;

    /**
     * @var string
     */
    protected $errorMessage;

    const LOCK_TIME = 30; // in seconds

    public function __construct(Request $request)
    {
        if (!$this->gameableClass) {
            throw new Exception('Gameable model should be explicitly set in the child class.');
        }

        if (!$this->apiClass) {
            throw new Exception('API class should be explicitly set in the child class.');
        }

        info(class_basename(get_called_class()) . ' ' . json_encode($request->all(), JSON_PRETTY_PRINT));

        $this->request = $request;
        $this->data = $this->parseData();
        $this->api = app()->make($this->apiClass);
        $this->user = User::where('code', $this->getUserId())->first();
        
        $client = new Client();
        
        $url = config('define.balance_dev.domain') . '/balance-game/' . $this->user->social_id;

        $res = $client->request('GET', $url);

        $body = $res->getBody()->getContents() ?? null;

        $body = isset($body) ? json_decode($body, true) : [];

        $this->balance = $body['data']['data']['balance'];
        $this->validateRequest();
    }

    protected function createProvablyFairGame(): ProvablyFairGame
    {
        $provablyFairGame = new ProvablyFairGame();
        $provablyFairGame->secret = '';
        $provablyFairGame->client_seed = random_int(10000, 99999);
        $provablyFairGame->gameable_type = $this->gameableClass;
        $provablyFairGame->save();

        return $provablyFairGame;
    }

    abstract protected function validateRequest();

    protected function getValue(string $key)
    {
        return data_get($this->data, $key);
    }

    public function requestIsValid(): bool
    {
        return !$this->errorMessage;
    }

    public function getHttpCode()
    {
        return $this->httpCode;
    }

    protected function loadGameable(): ?Model
    {
        return $this->gameableClass::where('external_id', $this->getExternalGameId())
            ->whereHas('game.account', function ($query) {
                $query->where('user_id', $this->user->id);
            })
            ->with('game')
            ->first();
    }

    public function process(): CallbackService
    {
        if ($this->getExternalGameId()) {
            $key = sprintf('game.%s.%s', class_basename($this->gameableClass), $this->getExternalGameId());
            // ensure only one request is processed at a time
            Cache::lock($key)->block(self::LOCK_TIME, Closure::fromCallable([$this, 'play']));
        }

        return $this;
    }

    abstract protected function getUserId(): ?string;

    abstract protected function getExternalGameId(): ?string;

    abstract protected function parseData();

    abstract protected function play(): CallbackService;

    abstract public function getSuccessResponse(): array;

    abstract public function getErrorResponse(): array;
}
