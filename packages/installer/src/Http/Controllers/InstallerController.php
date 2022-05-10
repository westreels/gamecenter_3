<?php

namespace Packages\Installer\Http\Controllers;

use App\Models\User;
use App\Services\DotEnvService;
use App\Services\LicenseService;
use App\Services\UserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Exception;
use Packages\Installer\Services\InstallerService;
use PDO;

class InstallerController extends Controller
{
    private $env;
    private $dotEnvService;
    private $ls;
    private $steps = [
        1 => 'license registration',
        2 => 'database setup',
        3 => 'admin account',
        4 => 'completed!',
    ];

    public function __construct(Request $request, DotEnvService $dotEnvService, LicenseService $ls)
    {
        $this->dotEnvService = $dotEnvService;
        $this->ls = $ls;

        if (!$this->dotEnvService->exists()) {
            try {
                $this->dotEnvService->create();
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }

        $this->env = $this->dotEnvService->get();

        // generate APP_KEY
        if (!env('APP_KEY')) {
            // it's important to use --force flag in all artisan commands when APP_ENV=production
            Artisan::call('key:generate', ['--force' => TRUE]);
        }

        // create necessary symlinks
        // note that is_link() returns FALSE for the symlink created by Laravel, so using file_exists() instead
        $storageLink = public_path('storage');
        $langLink = public_path('lang');

        if (!file_exists($storageLink) || !file_exists($langLink)) {
            // create symlinks defined in config/filesystems.php
            Artisan::call('storage:link');

            if (!file_exists($storageLink)) {
                die('Could not create public/storage --> storage/app/public symbolic link. Please check symlinks are allowed on your server.');
            }

            if (!file_exists($langLink)) {
                die('Could not create public/lang --> resources/lang symbolic link. Please check symlinks are allowed on your server.');
            }
        }
    }

    /**
     * Display installation step form
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function view(Request $request, $step)
    {
        // don't allow direct access to routes via GET, only through redirect
        if ($step > 1 && !$request->session()->has('app_redirect')) {
            return redirect()->route('install.view', ['step' => 1]);
        }

        return view('installer::pages.step' . $step, [
            'step' => $step,
            'title' => $this->steps[$step]
        ]);
    }

    /**
     * Process form on each step
     *
     * @param Request $request
     * @param $step
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process(Request $request, $step)
    {
        return call_user_func_array([$this, 'step' . $step], [$request, $step]);
    }

    public function step1(Request $request, $step)
    {
        $response = $this->ls->register($request->code, $request->email);

        if (!$response->success) {
            return back()->withInput()->withErrors($response->message)->with('app_redirect', TRUE);
        }

        $this->dotEnvService->save([FP_CODE => $request->code, FP_EMAIL => $request->email, FP_HASH => $response->message]);

        return redirect()
            ->route('install.view', ['step' => $step + 1])
            ->with('app_redirect', TRUE);
    }

    public function step2(Request $request, $step)
    {
        try {
            // check if DB connection can be created
            new PDO(
                'mysql:host='.$request->host.';port='.$request->port.';dbname='.$request->name,
                $request->username,
                $request->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // save DB settings
            $this->env['DB_HOST'] = $request->host;
            $this->env['DB_PORT'] = $request->port;
            $this->env['DB_DATABASE'] = $request->name;
            $this->env['DB_USERNAME'] = $request->username;
            $this->env['DB_PASSWORD'] = $request->password;
            $this->dotEnvService->save($this->env);

            // set current DB connection settings on the fly, otherwise Laravel doesn't recognize them
            config(['database.connections.mysql.host' => $request->host]);
            config(['database.connections.mysql.port' => $request->port]);
            config(['database.connections.mysql.database' => $request->name]);
            config(['database.connections.mysql.username' => $request->username]);
            config(['database.connections.mysql.password' => $request->password]);

            // it's important to purge current DB connection
            DB::purge('mysql');

            set_time_limit(1800);
            // run migrations and seeds
            Artisan::call('migrate:fresh', [
                '--force' => TRUE,
                '--seed' => TRUE,
            ]);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage())->with('app_redirect', TRUE);
        }

        return redirect()
            ->route('install.view', ['step' => $step + 1])
            ->with('app_redirect', TRUE);
    }

    public function step3(Request $request, $step)
    {
        try {
            // create user
            $user = UserService::create([
                'name'              => $request->name,
                'email'             => $request->email,
                'role'              => User::ROLE_ADMIN,
                'password'          => $request->password,
                'email_verified_at' => Carbon::now(),
            ]);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage())->with('app_redirect', TRUE);
        }

        event(new Registered($user));

        // switch off debug mode
        $this->env['APP_DEBUG'] = 'false';
        $this->env['APP_LOG_LEVEL'] = 'emergency';
        $this->env['SANCTUM_STATEFUL_DOMAINS'] = request()->getHost();
        $this->dotEnvService->save($this->env);

        // mark installation as completed
        touch(storage_path('installed'));

        try {
            $installerService = new InstallerService();
            $installerService->register();
        } catch (\Exception $e) {
            //
        }

        return redirect()
            ->route('install.view', ['step' => $step + 1])
            ->with('app_redirect', TRUE);
    }
}
