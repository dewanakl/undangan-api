<?php

namespace App\Repositories;

use App\Models\Like;
use Core\Model\Model;
use Ramsey\Uuid\Uuid;

class LikeRepositories implements LikeContract
{
    public function create(int $user_id, string $comment_id): Model
    {
        return Like::create([
            'uuid' => Uuid::uuid4()->toString(),
            'comment_id' => $comment_id,
            'user_id' => $user_id
        ]);
    }

    public function getByUuid(int $user_id, string $uuid): Model
    {
        return Like::where('uuid', $uuid)
            ->where('user_id', $user_id)
            ->limit(1)
            ->first();
    }

    public function deleteByCommentID(string $uuid): int
    {
        return Like::where('comment_id', $uuid)->delete();
    }

    public function countLikeByUserID(int $id): int
    {
        return Like::where('user_id', $id)->count('id', 'likes')->first()->likes;
    }
}
