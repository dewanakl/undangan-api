<?php

namespace App\Repositories;

use App\Models\Like;
use Core\Model\Model;
use Ramsey\Uuid\Uuid;

class LikeRepositories implements LikeContract
{
    public function create(int $userid, string $commentid): Model
    {
        return Like::create([
            'uuid' => Uuid::uuid4()->toString(),
            'comment_id' => $commentid,
            'user_id' => $userid
        ]);
    }

    public function getByUuid(int $userid, string $uuid): Model
    {
        return Like::where('uuid', $uuid)
            ->where('user_id', $userid)
            ->limit(1)
            ->first();
    }

    public function deleteByCommentID(string $uuid): int
    {
        return Like::where('comment_id', $uuid)->delete();
    }
}
