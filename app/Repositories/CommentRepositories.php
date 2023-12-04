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
            ->select(['uuid', 'nama', 'hadir', 'komentar', 'created_at'])
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
}
