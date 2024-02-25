<?php

namespace App\Repositories;

use App\Models\Comment;
use Core\Model\Model;
use Ramsey\Uuid\Uuid;

class CommentRepositories implements CommentContract
{
    public function create(array $data): Model
    {
        return Comment::create([
            'uuid' => Uuid::uuid4()->toString(),
            ...$data,
            'own' => Uuid::uuid4()->toString()
        ]);
    }

    public function getAll(int $userid, int $limit, int $offset): Model
    {
        return Comment::with('comments')
            ->select(['uuid', 'name', 'presence', 'comment', 'is_admin', 'created_at'])
            ->where('user_id', $userid)
            ->whereNull('parent_id')
            ->orderBy('id', 'DESC')
            ->limit(abs($limit))
            ->offset($offset)
            ->get();
    }

    public function getByUuid(int $userid, string $uuid): Model
    {
        return Comment::where('uuid', $uuid)
            ->where('user_id', $userid)
            ->limit(1)
            ->first();
    }

    public function getByOwnid(int $userid, string $ownid): Model
    {
        return Comment::where('own', $ownid)
            ->where('user_id', $userid)
            ->limit(1)
            ->first();
    }

    public function deleteByParrentID(string $uuid): int
    {
        return Comment::where('parent_id', $uuid)->delete();
    }

    public function countPresenceByUserID(int $id): Model
    {
        return Comment::where('user_id', $id)
            ->groupBy('user_id')
            ->select([
                'CAST(SUM(CASE WHEN presence = 1 THEN 1 ELSE 0 END) AS SIGNED) AS present_count',
                'CAST(SUM(CASE WHEN presence = 0 THEN 1 ELSE 0 END) AS SIGNED) AS absent_count'
            ])
            ->first();
    }

    public function downloadCommentByUserID(int $id): Model
    {
        return Comment::leftJoin('likes', 'comments.uuid', 'likes.comment_id')
            ->where('comments.user_id', $id)
            ->groupBy([
                'comments.uuid',
                'comments.name',
                'comments.presence',
                'comments.comment',
                'comments.ip',
                'comments.user_agent',
                'comments.created_at',
                'comments.parent_id'
            ])
            ->select([
                'comments.uuid',
                'count(likes.id) as is_like',
                'comments.name',
                'comments.presence',
                'comments.comment',
                'comments.ip',
                'comments.user_agent',
                'comments.created_at as is_created',
                'comments.parent_id'
            ])
            ->get();
    }
}
