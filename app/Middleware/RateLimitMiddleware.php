<?php

namespace App\Middleware;

use App\Response\JsonResponse;
use Closure;
use Core\Http\Request;
use Core\Middleware\MiddlewareInterface;
use MongoDB\Client;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Operation\FindOneAndUpdate;

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

        $limit = intval(env('RATE_LIMIT', 120));
        $window = intval(env('RATE_LIMIT_WINDOW', 60 * 60 * 24));

        $collection = (new Client(env('MONGODB_URI')))
            ->selectDatabase(env('MONGODB_DB'))
            ->selectCollection(env('MONGODB_COLLECTION'));

        $this->createIndexIfnotExists($collection, $window);

        $result = $collection->findOneAndUpdate(
            [
                'ip' => context('ip'),
                'window_start' => ['$gte' => new UTCDateTime((time() - $window) * 1000)]
            ],
            [
                '$inc' => ['count' => 1],
                '$setOnInsert' => ['window_start' => new UTCDateTime(time() * 1000)]
            ],
            [
                'upsert' => true,
                'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER
            ]
        );

        if ($result && intval($result['count']) > $limit) {
            $collection->updateOne(
                ['_id' => $result['_id']],
                ['$inc' => ['count' => -1]]
            );

            $dateFormat = $result['window_start']->toDateTime()
                ->modify(sprintf('+%d seconds', $window))
                ->format('Y-m-d H:i:s T');

            return (new JsonResponse)->errorBadRequest([
                sprintf('Too many requests. Please wait until %s.', $dateFormat),
            ]);
        }

        return $next($request);
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

            if (isset($index['expireAfterSeconds']) && $key === ['window_start' => 1]) {
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
                ['window_start' => 1],
                ['expireAfterSeconds' => $expectedTtl]
            );
        }
    }
}
