<?php

namespace App\Http\Controllers;

use App\Helpers\PackageManager;
use App\Models\Game;
use App\Services\OAuthService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    public function index($path = NULL, Request $request, OAuthService $OAuthService, PackageManager $packageManager)
    {
        $variables = [
            'config' => array_merge(
                $this->mapConfigVariables('app', ['name', 'version', 'logo', 'banner', 'url', 'locale', 'default_locale']),
                $this->mapConfigVariables('broadcasting', ['connections.pusher.key', 'connections.pusher.options.cluster']),
                $this->mapConfigVariables('settings', ['theme', 'interface', 'content', 'format', 'games', 'bonuses', 'affiliate', 'email_verification']),
                $this->mapConfigVariables('services', ['recaptcha.public_key']),
                ['oauth' => $OAuthService->getEnabled(['client_id', 'mdi'])]
            ),
            'user' => $request->user() ? UserService::user($request->user()) : NULL,
            'games' => [
                'count' => Game::completed()->count(),
                'biggest_win' => Cache::remember('games.biggest_win', 3600, function () {
                    return tap(
                        Game::with('account:id,user_id', 'account.user:id,name,email,avatar') // email is required for avatar_url to work
                            ->completed()
                            ->profitable()
                            ->orderBy('win', 'desc')
                            ->limit(1)
                            ->first(),
                        function (?Game $game) {
                            if ($game) {
                                $game->account->user->makeHidden(['email']);
                            }
                        });
                    }),
                'last_win' => tap(
                    Game::with('account:id,user_id','account.user:id,name,email,avatar') // email is required for avatar_url to work
                        ->completed()
                        ->profitable()
                        ->orderBy('id', 'desc')
                        ->limit(1)
                        ->first(),
                    function (?Game $game) {
                        if ($game) {
                            $game->account->user->makeHidden(['email']);
                        }
                    })
            ],
            'assets' => [
                'deck' =>  config('settings.games.playing_cards.deck')
                    ? collect(Storage::disk('assets')->files(sprintf('images/games/playing-cards/%s', config('settings.games.playing_cards.deck'))))
                        ->map(function ($path) {
                            return url($path);
                        })
                        ->toArray()
                    : []
            ]
        ];

        // load only locales that are present
        $variables['config']['app']['locales'] = collect(config('app.locales'))
            ->filter(function ($locale, $code) {
                return Storage::disk('resources')->exists('lang/' . $code . '.json');
            })
            ->toArray();

        // pass named routes
        // note that prefix is not added to routes using this method
        $namedRoutes = Route::getRoutes()->getRoutesByName();

        $variables['routes'] = array_combine(
            array_keys($namedRoutes),
            array_map(function ($route) {
                return '/' . $route->uri;
            }, $namedRoutes)
        );

        // pass enabled packages (add-ons)
        $enabledPackages = $packageManager->getEnabled();

        $variables['packages'] = array_combine(
            array_keys($enabledPackages),
            array_map(function ($package) {
                return [
                    'type' => $package->type,
                    'name' => __($package->name)
                ];
            }, $enabledPackages)
        );

        // extra add-ons config
        foreach ($packageManager->getEnabled() as $package) {
            $packageConfig = [];

            // provide only public variables if they are specified in the config file
            if ($publicVariables = config($package->id . '.public_variables')) {
                collect($publicVariables)->each(function ($key) use ($package, &$packageConfig) {
                    // set a missing value within a nested array or object using "dot" notation:
                    data_fill($packageConfig, $key, config($package->id . '.' . $key));
                });
            } else {
                $packageConfig = config($package->id);
            }

            // add translations for game titles
            if (isset($packageConfig['variations'])) {
                collect($packageConfig['variations'])->each(function ($variation) {
                    $variation->_title = __($variation->title);
                });
            }

            $variables['config'][$package->id] = $packageConfig;
        }

        return view('index', $variables);
    }

    public function show(string $page)
    {
        $file = 'html/' . preg_replace('#[^a-z0-9-_]#i', '', $page) . '.html';

        $html = Storage::disk('public')->exists($file)
            ? Storage::disk('public')->get($file)
            : (Storage::disk('assets')->exists($file)
                ? Storage::disk('assets')->get($file)
                : NULL);

        return response()->json([
            'html' => $html
        ]);
    }

    public function recentGames()
    {
        return Game::completed()
            ->orderBy('created_at', 'desc')
            ->take(10)
            // Load relations with specific columns (such as name) that can be safely shared with all users.
            ->with('account:id,user_id', 'account.user:id,name,email,avatar') // email is required for avatar_url to work
            ->get()
            ->map(function (Game $game) {
                $game->account->user->makeHidden(['email']);
                return $game;
            });
    }

    /**
     * Get specific config variables
     *
     * mapConfigVariables('app', ['name', 'locale']) ==> ['app' => ['name' => config('app.name'), 'locale' => config('app.locale')]]
     *
     * @param $key
     * @param $array
     * @return array
     */
    protected function mapConfigVariables ($key, $array)
    {
        $result = [];

        foreach ($array as $item) {
            data_set($result, $key . '.' . $item, data_get(config($key), $item));
        }

        return $result;
    }
}
