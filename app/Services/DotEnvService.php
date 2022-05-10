<?php

namespace App\Services;

use Illuminate\Support\Str;

class DotEnvService
{
    const ENV = '.env';
    const ENV_INSTALL = '.env.install';

    private $env;

    public function __construct()
    {
        $this->env = $_ENV;

        return $this;
    }

    public function exists()
    {
        return file_exists($this->getEnvFilePath());
    }

    /**
     * Create .env file from the default .env.install provided with the app
     *
     * @return DotEnvService
     * @throws \Exception
     */
    public function create(): DotEnvService
    {
        $baseEnvFilePath = base_path() . '/' . self::ENV_INSTALL;

        if (!file_exists($baseEnvFilePath)) {
            throw new \Exception('.env.install file does not exist. Please make sure you copied all files to the server.');
        }

        if (!is_writable(base_path())) {
            throw new \Exception('Please make sure the web root folder is writable: ' . base_path());
        }

        if (!copy($baseEnvFilePath, $this->getEnvFilePath())) {
            throw new \Exception('Could not create .env file, please check permissions.');
        }

        return $this;
    }

    protected function getEnvFilePath()
    {
        return base_path() . '/' . self::ENV;
    }

    public function get()
    {
        return $this->env;
    }

    /**
     * Save config variables to env file
     * @param array $params
     * @return bool
     */
    public function save(array $params): bool
    {
        if (!is_writable($this->getEnvFilePath())) {
            return FALSE;
        }

        // include string into double quotes in certain cases
        $variableToString = function($value) {
            $type = gettype($value);

            $string = in_array($type, ['array','object'])
                ? json_encode(array_map(function($v) {
                        return is_string($v) && Str::startsWith($v, ['[','{']) ? json_decode($v) : $v;
                    }, $value), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) // don't escape slashes in file paths
                : $value;

            // if string is a hex color code or contains # symbol
            if ($type == 'string'
                && !Str::contains($string, '"')
                && (preg_match('#^\#[a-f\d]{3,6}$#i', $string) || Str::contains($string, '#'))) {
                $string = '"' . $string . '"';
            } else {
                // if string contains non escaped white spaces or #
                if (Str::contains($string, [' ', '#']) && !Str::contains($string, '\\"')) {
                    $string = '"' . addcslashes($string, '"') . '"';
                }
            }

            return $string;
        };

        $this->env = array_map($variableToString, array_merge($this->env, $params));

        return file_put_contents($this->getEnvFilePath(), implode("\n", array_map(function ($key, $value) {
            return $key . '=' . $value;
        }, array_keys($this->env), $this->env)));
    }
}
