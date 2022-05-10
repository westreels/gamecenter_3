<?php

namespace App\Http;

use App\Http\Middleware\LockMultiplayerGame;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
//        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\SetLocale::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            'bindings'
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        '2fa' => \App\Http\Middleware\TwoFactorAuthentication::class,
        'active' => \App\Http\Middleware\LogoutIfBlocked::class,
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'concurrent' => \App\Http\Middleware\BlockConcurrentRequests::class,
        'cookies' => \App\Http\Middleware\EncryptCookies::class,
        'game.lock' => LockMultiplayerGame::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'last_seen' => \App\Http\Middleware\UpdateLastSeen::class,
        'maintenance' => \App\Http\Middleware\CheckForMaintenanceMode::class,
        'page.enabled' => \App\Http\Middleware\CheckPageIsEnabled::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'permissions' => \App\Http\Middleware\CheckPermissions::class,
        'referrer' => \App\Http\Middleware\RememberReferrerUser::class,
        'role' => \App\Http\Middleware\CheckRole::class,
        'room.lock' => \App\Http\Middleware\LockGameRoom::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'social' => \App\Http\Middleware\CheckSocialProvider::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
    ];
}
