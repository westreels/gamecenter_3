<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use ReflectionClass;

class Utils
{
    /**
     * Get an array of Carbon dates based on the period
     *
     * @param string $period
     * @return array
     */
    public static function getDateRange(?string $period): array
    {
        if ($period == 'day') {
            return [Carbon::now()->startOfDay(), Carbon::now()];
        } elseif ($period == 'prev_day') {
            return [Carbon::now()->subDay()->startOfDay(), Carbon::now()->startOfDay()->subSecond()];
        } elseif ($period == 'prev_week') {
            return [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->startOfWeek()->subSecond()];
        } elseif ($period == 'month') {
            return [Carbon::now()->startOfMonth(), Carbon::now()];
        } elseif ($period == 'prev_month') {
            return [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->startOfMonth()->subSecond()];
        } elseif ($period == 'year') {
            return [Carbon::now()->startOfYear(), Carbon::now()];
        } elseif ($period == 'prev_year') {
            return [Carbon::now()->subYear()->startOfYear(), Carbon::now()->startOfYear()->subSecond()];
        // $period = 'week' is the default value
        } else {
            return [Carbon::now()->startOfWeek(), Carbon::now()];
        }
    }

    public static function assert($class, $hash, $cb)
    {
        try {
            return Cache::remember('hash_' . class_basename($class), 300, function () use ($class, $hash) {
                return sha1(preg_replace('#\s+#', '', file_get_contents((new ReflectionClass($class))->getFileName()))) == $hash;
            }) ?: $cb();
        } catch (Exception $e) {
            //
        }
    }

    /**
     * Get class constant name by its instance value
     *
     * @param  string  $class
     * @param  object  $instance
     * @param $value
     * @return string
     * @throws \ReflectionException
     */
    public static function getConstantNameByValue(string $class, object $instance, $value): string
    {
        $r = new ReflectionClass($class);

        return collect($r->getConstants())
            ->filter(function ($constantValue, $constantName) use ($value) {
                return $value === $constantValue;
            })
            ->keys()
            ->first();
    }

    /**
     * Get full absolute path to PHP executable
     *
     * @return string
     */
    public static function getPathToPhp(): string
    {
        return PHP_BINDIR . DIRECTORY_SEPARATOR . 'php';
    }

    /**
     * Get cron job command that should be added to the server
     *
     * @return string
     */
    public static function getCronJobCommand(): string
    {
        return '* * * * * ' . self::getPathToPhp() . ' -d register_argc_argv=On ' . base_path() . DIRECTORY_SEPARATOR . 'artisan schedule:run';
    }

    public static function generateRandomString(int $numberOfBytes): string
    {
        return bin2hex(random_bytes($numberOfBytes));
    }
}
