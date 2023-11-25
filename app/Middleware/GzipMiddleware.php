<?php

namespace App\Middleware;

use Closure;
use Core\Http\Request;
use Core\Http\Stream;
use Core\Middleware\MiddlewareInterface;

final class GzipMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Stream|\Core\Http\Respond
    {
        $response = $next($request);

        if ($response instanceof Stream) {
            return $response;
        }

        $response = respond()->transform($response);

        if (env('GZIP', 'true') != 'true') {
            return $response;
        }

        if (!in_array('gzip', explode(', ', $request->server->get('HTTP_ACCEPT_ENCODING')))) {
            return $response;
        }

        $compressed = gzencode($response->getContent(false), 1);

        if ($compressed === false) {
            return $response;
        }

        $response->setContent($compressed);

        if ($response->headers->has('Vary')) {
            $vary = explode(', ', $response->headers->get('Vary'));
            $vary = array_unique([...$vary, 'Accept-Encoding']);
            $response->headers->set('Vary', join(', ', $vary));
        } else {
            $response->headers->set('Vary', 'Accept-Encoding');
        }

        $response->headers
            ->set('Content-Encoding', 'gzip')
            ->set('Content-Length', strlen($compressed));

        return $response;
    }
}
