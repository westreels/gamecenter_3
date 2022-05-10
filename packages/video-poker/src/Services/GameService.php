<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace Packages\VideoPoker\Services;

use App\Helpers\Games\CardDeck;
use App\Helpers\Games\CardSet;
use App\Models\Game;
use App\Models\User;
use App\Services\GameService as ParentGameService;
use Packages\VideoPoker\Helpers\PokerHand;
use Packages\VideoPoker\Models\VideoPoker;
use App\Http\Traits\CallApiTraits;
use Illuminate\Support\Facades\Auth;

class GameService extends ParentGameService
{
    use CallApiTraits;

    protected $gameableClass = VideoPoker::class;

    protected $deck;

    public function makeSecret(): string
    {
        return implode(',', (new CardDeck())->toArray());
    }

    /**
     * Deal initial set of cards
     *
     * @param $params
     */
    public function play($params): GameService
    {
        $provablyFairGame = $this->getProvablyFairGame();

        // load initially shuffled deck
        $deck = new CardDeck(explode(',', $provablyFairGame->secret));
        // cut the deck (provably fair)
        $deck->cut($provablyFairGame->shift_value % 52);
        $initialDeck = $deck->toArray();

//        TESTING
//        -------
//
//        ROYAL FLUSH
//        $deck = $deck->replace(['HA','HJ','HQ','HK','HT']);
//        ---------------------------------------------------------------------

        $hand = new PokerHand($deck->dealSet(5));

        $gameable = new $this->gameableClass();
        $gameable->deck = $initialDeck;
        $gameable->hand = $hand->getCards()->toArray();
        $gameable->combination = $hand->isPairOfJacksOrBetter() ? $hand->getRank() : PokerHand::HAND_HIGH_CARD;

        // important to save a reference to gameable in the class property, so it can be used in the parent class
        $this->gameable = $gameable;

        // save game
        $this->save(['bet' => $params['bet'], 'win' => 0, 'status' => Game::STATUS_IN_PROGRESS]);

        return $this;
    }

    /**
     * Hold some cards and finish the game
     *
     * @return GameService
     */
    public function draw($params): GameService
    {
        dd('abc');
        $gameable = $this->getGameable();

        $deck = (new CardDeck($gameable->deck))->remove(5);

        // sort hold indexes
        $gameable->hold = collect($params['hold'])->sort()->values();

        if ($gameable->hold->count() < 5) {
            $gameable->hand = $gameable->hand->map(function ($card, $i) use ($gameable, $deck) {
                return $gameable->hold->search($i) !== FALSE ? $card : $deck->deal()->code;
            });
        }

        $hand = new PokerHand(new CardSet($gameable->hand));

        $gameable->combination = $hand->isPairOfJacksOrBetter() ? $hand->getRank() : PokerHand::HAND_HIGH_CARD;
        $win = $this->getGame()->bet * (config('video-poker.paytable')[$gameable->combination] ?? 0);

        $url = config('define.api_balance.domain') . '/api/get-balance/social/' . Auth::user()->social_id;

        $body = $this->callapi('GET', $url, []);

        $this->getGame()->account['balance'] = $body['balance'];

        $this->save(['win' => $win, 'status' => Game::STATUS_COMPLETED]);

        return $this;
    }

    public static function createRandomGame(User $user): void
    {
        $instance = new self($user);

        $instance->createProvablyFairGame(random_int(10000,99999));

        $minBet = intval(config('settings.bots.games.min_bet'));
        $maxBet = intval(config('settings.bots.games.max_bet'));

        $instance
            ->play(['bet' => random_int($minBet ?: config('video-poker.min_bet'), $maxBet ?: config('video-poker.max_bet'))])
            ->unsetGameable();

        $hand = new PokerHand(new CardSet($instance->getGameable()->hand));

        // hold main cards in the hand, i.e. high card, pair etc
        $hold = $hand->get()
            ->filter(function ($card) use ($hand) {
                return !$hand->getKickers()->contains($card->code);
            });

        $instance->draw([
            'hold' => $hold->toCollection()->map(function ($card) use ($instance) {
                // find card index in the original hand collection
                return $instance->getGameable()->hand->search($card);
            })
        ]);
    }
}
