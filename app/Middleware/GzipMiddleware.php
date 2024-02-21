<?php

namespace App\Middleware;

use Closure;
use Core\Http\Request;
use Core\Http\Respond;
use Core\Http\Stream;
use Core\Middleware\MiddlewareInterface;

final class GzipMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Stream|Respond
    {
        $response = $next($request);

        if ($response instanceof Stream) {
            return $response;
        }

        if ($response instanceof Respond && $response->getCode() < 400 && $response->getCode() >= 300) {
            return $response;
        }

        $response = respond()->transform($response);

        if (env('GZIP', 'true') != 'true') {
            return $response;
        }

        if (!str_contains($request->server->get('HTTP_ACCEPT_ENCODING', ''), 'gzip')) {
            return $response;
        }

        $compressed = gzencode($response->getContent(false), 1);

        if ($compressed === false) {
            return $response;
        }

        $response->setContent($compressed);

        $vary = (!$response->headers->has('Vary')) ? [] : explode(', ', $response->headers->get('Vary'));
        $vary = array_unique([...$vary, 'Accept-Encoding']);

        $response->headers
            ->set('Vary', join(', ', $vary))
            ->set('Content-Encoding', 'gzip')
            ->set('Content-Length', strlen($compressed));

        return $response;
    }
}
