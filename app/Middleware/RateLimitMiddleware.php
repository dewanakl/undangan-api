<?php

namespace App\Middleware;

use App\Response\JsonResponse;
use Closure;
use Core\Http\Request;
use Core\Http\Respond;
use Core\Http\Stream;
use Core\Middleware\MiddlewareInterface;
use MongoDB\Client;
use MongoDB\BSON\UTCDateTime;

final class RateLimitMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        if (!env('MONGODB_URI') || !env('MONGODB_DB') || !env('MONGODB_COLLECTION')) {
            return $next($request);
        }

        if (!class_exists(Client::class) || !class_exists(UTCDateTime::class)) {
            throw new \Exception('MongoDB PHP Library is not installed.');
        }

        list($response, $headers) = $this->handleRateLimit($request, $next);

        $baseResponse = ($response instanceof Stream) ? respond() : $response;

        foreach ($headers as $key => $value) {
            $baseResponse->headers->set($key, $value);
        }

        return $response;
    }

    public function handleRateLimit(Request $request, Closure $next)
    {
        $limit = intval(env('RATE_LIMIT', 120));
        $window = intval(env('RATE_LIMIT_WINDOW', 60 * 60 * 24));

        $collection = (new Client(env('MONGODB_URI')))
            ->selectDatabase(env('MONGODB_DB'))
            ->selectCollection(env('MONGODB_COLLECTION'));

        $this->createIndexIfnotExists($collection, $window);

        $record = $collection->findOne([
            'ip' => context('ip'),
            'window_start_time' => ['$gte' => new UTCDateTime((time() - $window) * 1000)]
        ]);

        $rateLimitHeaders = [
            'X-Rate-Limit-Value' => sprintf('%d/%d', $limit, $limit),
            'X-Rate-Limit-Reset' => time() + $window
        ];

        $response = $next($request);

        if (
            !$response instanceof Stream &&
            !($response instanceof Respond && $response->getCode() >= 300 && $response->getCode() < 400)
        ) {
            $response = respond()->transform($response);
        }

        if (!$record) {
            $collection->findOneAndUpdate(
                ['ip' => context('ip')],
                ['$set' => [
                    'count' => 1,
                    'window_start_time' => new UTCDateTime(time() * 1000)
                ]],
                ['upsert' => true]
            );

            return [$response, $rateLimitHeaders];
        }

        $rateLimitHeaders['X-Rate-Limit-Value'] = sprintf('%d/%d', $limit - intval($record['count']), $limit);
        $rateLimitHeaders['X-Rate-Limit-Reset'] = $record['window_start_time']->toDateTime()->getTimestamp() + $window;

        if (intval($record['count']) >= $limit) {
            $response = (new JsonResponse)->errorBadRequest(['Rate limit exceeded. Please try again later.']);

            return [$response, $rateLimitHeaders];
        }

        $collection->updateOne(
            ['_id' => $record['_id']],
            ['$inc' => ['count' => 1]]
        );

        return [$response, $rateLimitHeaders];
    }

    private function createIndexIfnotExists($collection, $window)
    {
        $ipIndexExists = false;
        $ttlIndexName = null;
        $existingExpireAfter = null;

        foreach ($collection->listIndexes() as $index) {
            $key = $index->getKey();

            if ($key === ['ip' => 1]) {
                $ipIndexExists = true;
            }

            if (isset($index['expireAfterSeconds']) && $key === ['window_start_time' => 1]) {
                $ttlIndexName = $index->getName();
                $existingExpireAfter = $index['expireAfterSeconds'];
            }
        }

        if (!$ipIndexExists) {
            $collection->createIndex(['ip' => 1]);
        }

        $expectedTtl = $window * 2;
        if ($ttlIndexName && $existingExpireAfter !== $expectedTtl) {
            $collection->dropIndex($ttlIndexName);
        }

        if (!$ttlIndexName || $existingExpireAfter !== $expectedTtl) {
            $collection->createIndex(
                ['window_start_time' => 1],
                ['expireAfterSeconds' => $expectedTtl]
            );
        }
    }
}
