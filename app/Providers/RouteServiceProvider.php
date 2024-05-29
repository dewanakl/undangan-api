<?php

namespace App\Providers;

use App\Middleware\CookieMiddleware;
use App\Middleware\CsrfMiddleware;
use Core\Database\DB;
use Core\Facades\Provider;
use Core\Routing\Route;
use Core\Routing\Router;
use Exception;

class RouteServiceProvider extends Provider
{
    /**
     * Prefix api.
     *
     * @var string $API_PREFIX
     */
    public static $API_PREFIX = '/api';

    /**
     * Jalankan sewaktu aplikasi dinyalakan.
     *
     * @return void
     */
    public function booting()
    {
        $this->app->singleton(Router::class);
        if (!Route::setRouteFromCacheIfExist()) {
            Route::middleware(CsrfMiddleware::class)->group(function () {
                Route::setRouteFromFile();
            });

            Route::prefix(static::$API_PREFIX)->middleware(CookieMiddleware::class)->group(function () {
                Route::get('/health', [static::class, 'health']);
                require_once base_path('/routes/api.php');
            });
        }
    }

    /**
     * Get health this application.
     *
     * @return \Core\Http\Respond
     */
    public function health(): \Core\Http\Respond
    {
        $data = [
            'status' => true
        ];

        if (!hash_equals(hash('sha3-512', env('APP_KEY')), request()->get('hash', ''))) {
            return respond()->setContent(json($data, 200));
        }

        $data['system'] = [
            'php_version' => PHP_VERSION,
            'execute_time' => execute_time(),
            'memory_peak_usage' => format_bytes(memory_get_peak_usage(true)),
        ];

        $data['system']['opcache'] = [
            'opcache_enabled' => false,
            'opcache_used_memory' => format_bytes(0),
            'opcache_free_memory' => format_bytes(0),
            'jit' => [
                'enabled' => false,
                'buffer_size' => format_bytes(0),
                'buffer_free' => format_bytes(0),
            ]
        ];

        if (function_exists('opcache_get_status')) {
            $opcache = opcache_get_status();
            $data['system']['opcache'] = [
                'opcache_enabled' => $opcache['opcache_enabled'],
                'opcache_used_memory' => format_bytes($opcache['memory_usage']['used_memory']),
                'opcache_free_memory' => format_bytes($opcache['memory_usage']['free_memory']),
                'jit' => [
                    'enabled' => $opcache['jit']['enabled'],
                    'buffer_size' => format_bytes($opcache['jit']['buffer_size']),
                    'buffer_free' => format_bytes($opcache['jit']['buffer_free']),
                ],
            ];
        }

        $data['database'] = [
            'server_version' => null,
            'client_version' => null,
            'connection_status' => null,
            'server_info' => null,
            'error' => null,
        ];

        if (
            env('DB_DRIV') &&
            env('DB_HOST') &&
            env('DB_NAME') &&
            env('DB_PORT') &&
            env('DB_USER')
        ) {
            try {
                $database = DB::getInfoDriver();
                $data['database'] = [
                    'server_version' => $database['SERVER_VERSION'],
                    'client_version' => $database['CLIENT_VERSION'],
                    'connection_status' => $database['CONNECTION_STATUS'],
                    'server_info' => $database['SERVER_INFO'],
                    'error' => null,
                ];

                $data['system']['execute_time'] = execute_time();
            } catch (Exception $e) {
                $data['status'] = false;
                $data['database'] = [
                    'server_version' => null,
                    'client_version' => null,
                    'connection_status' => null,
                    'server_info' => null,
                    'error' => $e->getMessage(),
                ];

                return respond()->setContent(json($data, 500));
            }
        }

        return respond()->setContent(json($data, 200));
    }
}
