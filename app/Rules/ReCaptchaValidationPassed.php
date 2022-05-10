<?php

namespace App\Rules;

use GuzzleHttp\Client;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ReCaptchaValidationPassed implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Validate ReCaptcha
        $client = new Client([
            'base_uri' => 'https://google.com/recaptcha/api/'
        ]);
        $response = $client->post('siteverify', [
            'query' => [
                'secret'    => config('services.recaptcha.secret_key'),
                'response'  => $value,
                'remoteip'  => request()->ip(),
            ]
        ]);

        Log::info($response->getBody());

        $responseBody = json_decode($response->getBody());

        return $responseBody->success ?? FALSE;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('Please verify that you are a human.');
    }
}
