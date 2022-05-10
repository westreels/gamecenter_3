<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CommandManager;
use App\Helpers\Queries\FailedJobQuery;
use App\Helpers\Queries\JobQuery;
use App\Helpers\ReleaseManager;
use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Services\ArtisanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MaintenanceController extends Controller
{
    const LOG_FILE_EXTENSION = '.log';

    public function index(CommandManager $commandManager, ReleaseManager $releaseManager)
    {
        $latestVersion = data_get($releaseManager->getInfo(), 'app.version');

        return response()->json([
            'current_version'   => config('app.version'),
            'latest_version'    => $latestVersion,
            'update_available'  => $latestVersion && version_compare(config('app.version'), $latestVersion, '<'),
            'system_info'       => [
                ['title' => __('OS'), 'value' => php_uname()],
                ['title' => __('PHP version'), 'value' => PHP_VERSION],
                ['title' => __('Path to PHP'), 'value' => Utils::getPathToPhp()],
                ['title' => __('Installation folder'), 'value' => base_path()],
            ],
            'log_files' => collect(Storage::disk('logs')->files())
                ->filter(function ($path) {
                    return Str::of($path)->endsWith(self::LOG_FILE_EXTENSION);
                })
                ->map(function ($path) {
                    return Str::of(basename($path))->replace(self::LOG_FILE_EXTENSION, '');
                })
                ->values(),
            'maintenance_mode'  => app()->isDownForMaintenance(),
            'commands'          => $commandManager->all(),
            'queues'            => [STAKE_QUEUE_MULTIPLAYER_GAMES, 'default'],
            'cron_job'          => Utils::getCronJobCommand()
        ]);
    }

    /**
     * Enable maintenance mode
     *
     * @param Request $request
     * @return array
     */
    public function down(Request $request)
    {
        Artisan::call('down');

        return $this->successResponse($this->getSuccessMessage());
    }

    /**
     * Disable maintenance mode
     *
     * @return array
     */
    public function up()
    {
        Artisan::call('up');

        return $this->successResponse($this->getSuccessMessage());
    }

    /**
     * Run migrations
     *
     * @return array
     */
    public function migrate()
    {
        set_time_limit(1800);
        ArtisanService::migrateAndSeed();

        return $this->successResponse($this->getSuccessMessage());
    }

    /**
     * Clear all caches
     *
     * @return array
     */
    public function cache()
    {
        set_time_limit(1800);
        ArtisanService::clearAllCaches();

        return $this->successResponse($this->getSuccessMessage());
    }

    /**
     * Run cron
     *
     * @return array
     */
    public function cron()
    {
        set_time_limit(1800);
        Artisan::call('schedule:run');

        return $this->successResponse($this->getSuccessMessage());
    }

    public function command(Request $request, CommandManager $commandManager)
    {
        $command = $commandManager->get($request->command);

        $request->validate([
            'command' => [
                'required',
                Rule::in([$command['class']])
            ]
        ]);

        set_time_limit(1800);

        // ensure only supported arguments are passed
        $arguments = collect($request->arguments);
        $params = $arguments->only(array_column($command['arguments'], 'id'))->all();

        // execute artisan command
        Artisan::call($command['signature'], $params);

        // get command output
        $output = Artisan::output();

        return $this->successResponse(Str::of($output)->trim()->length() > 0 ? $output : $this->getSuccessMessage());
    }

    public function jobs(JobQuery $query)
    {
        return [
            'count' => $query->getRowsCount(),
            'items' => $query->get()
        ];
    }

    public function failedJobs(FailedJobQuery $query)
    {
        return [
            'count' => $query->getRowsCount(),
            'items' => $query->get()
        ];
    }

    public function clearQueue(Request $request)
    {
        Artisan::call('queue:clear', [
            '--queue' => $request->queue,
            '--force' => TRUE
        ]);

        return $this->successResponse($this->getSuccessMessage());
    }

    public function stopQueueWorker()
    {
        Artisan::call('queue:restart');

        return $this->successResponse($this->getSuccessMessage());
    }

    public function getLogFile(Request $request)
    {
        $storage = Storage::disk('logs');
        $file = $request->file . self::LOG_FILE_EXTENSION;

        return $storage->exists($file) ? $storage->get($file) : '';
    }

    public function deleteLogFile(Request $request)
    {
        $storage = Storage::disk('logs');
        $file = $request->file . self::LOG_FILE_EXTENSION;

        if (!$storage->exists($file)) {
            abort(404);
        }

        return $storage->delete($file)
            ? $this->successResponse(__('Log file successfully deleted.'))
            : $this->errorResponse(__('Log file can not be deleted.'));
    }

    public function downloadLogFile(Request $request)
    {
        $file = $request->file . self::LOG_FILE_EXTENSION;

        if (!Storage::disk('logs')->exists($file)) {
            abort(404);
        }

        return response()->download(storage_path(sprintf('logs/%s', $file)));
    }

    protected function getSuccessMessage()
    {
        return __('Operation performed successfully.');
    }
}
