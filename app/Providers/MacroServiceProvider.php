<?php

namespace App\Providers;

use Illuminate\Http\Response;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Response::macro(
            'api',
            function (
                int $responseCode,
                int $success,
                $data,
                $error = null,
                array|MessageBag $errors = [],
                array $trace = []
            ) {
                return response()->json(
                    [
                        'success' => $success,
                        'data' => $data,
                        'error' => $error,
                        'errors' => $errors,
                        'trace' => $trace,
                    ],
                    $responseCode
                );
            }
        );
    }
}
