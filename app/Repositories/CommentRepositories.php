<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Models\Like;
use Core\Database\DB;
use Core\Model\Model;
use Exception;
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

    public function getAll(int $user_id, bool $is_admin, string $user_name, int $limit, int $offset): array
    {
        $selectedFields = [
            'comments.uuid',
            'comments.name',
            'comments.presence',
            'comments.comment',
            'comments.is_admin',
            'comments.gif_url',
            'comments.created_at',
        ];

        if ($is_admin) {
            $selectedFields = [
                ...$selectedFields,
                'comments.ip',
                'comments.own',
                'comments.user_agent'
            ];
        }

        /**
         * @param Comment<int, object>|array<int, object> $comments
         * @return array<int, object>
         */
        $buildTree = static function ($comments) use (&$buildTree, $selectedFields, $user_id, $user_name): array {
            $uuids = [];
            foreach ($comments as $comment) {
                $uuids[] = $comment->uuid;
            }

            $grouped = [];
            if (count($uuids) > 0) {
                Comment::leftJoin('likes', 'comments.uuid', 'likes.comment_id')
                    ->whereIn('comments.parent_id', $uuids)
                    ->where('comments.user_id', $user_id)
                    ->select($selectedFields)
                    ->select(['comments.id', 'false as is_parent', 'comments.parent_id', 'count(likes.id) as like_count'])
                    ->groupBy(['comments.id', ...$selectedFields])
                    ->orderBy('comments.id')
                    ->get()
                    ->map(function ($child) use (&$grouped): void {
                        $grouped[$child->parent_id][] = $child;
                    });
            }

            $results = [];
            foreach ($comments as $comment) {
                $comment->comments = isset($grouped[$comment->uuid]) ? $buildTree($grouped[$comment->uuid]) : [];

                if ($comment->is_admin) {
                    $comment->name = $user_name;
                }

                unset($comment->id);
                unset($comment->parent_id);
                $results[] = $comment;
            }

            return $results;
        };

        $parents = Comment::leftJoin('likes', 'comments.uuid', 'likes.comment_id')
            ->whereNull('comments.parent_id')
            ->where('comments.user_id', $user_id)
            ->select($selectedFields)
            ->select(['comments.id', 'true as is_parent', 'comments.parent_id', 'count(likes.id) as like_count'])
            ->groupBy(['comments.id', ...$selectedFields])
            ->orderBy('comments.id', 'DESC')
            ->limit(abs($limit))
            ->offset($offset)
            ->get();

        return $buildTree($parents);
    }

    public function count(int $user_id): int
    {
        return intval(Comment::select('count(id) as comment_count')
            ->where('user_id', $user_id)
            ->whereNull('parent_id')
            ->first()
            ->comment_count);
    }

    public function getByUuid(int $user_id, string $uuid): Model
    {
        return Comment::where('uuid', $uuid)
            ->where('user_id', $user_id)
            ->limit(1)
            ->first();
    }

    public function getByOwnId(int $user_id, string $own_id): Model
    {
        return Comment::where('own', $own_id)
            ->where('user_id', $user_id)
            ->limit(1)
            ->first();
    }

    public function deleteAllByUuid(int $userId, string $uuid): bool
    {
        $commentUuids = [$uuid];
        $nextParents = [$uuid];

        while (count($nextParents) > 0) {
            $comments = Comment::where('user_id', $userId)
                ->whereIn('parent_id', $nextParents)
                ->select('uuid')
                ->get();

            $nextParents = [];
            foreach ($comments as $comment) {
                $commentUuids[] = $comment->uuid;
                $nextParents[] = $comment->uuid;
            }
        }

        return DB::transaction(function () use ($commentUuids, $userId): bool {

            Like::where('user_id', $userId)
                ->whereIn('comment_id', $commentUuids)
                ->delete();

            $deletedComments = Comment::where('user_id', $userId)
                ->whereIn('uuid', $commentUuids)
                ->delete();

            if (count($commentUuids) === $deletedComments) {
                return true;
            }

            throw new Exception('Uncompleted deletion: comments=' . strval($deletedComments));
        });
    }

    public function deleteByParentID(string $uuid): int
    {
        return Comment::where('parent_id', $uuid)->delete();
    }

    public function countCommentByUserID(int $id): int
    {
        return Comment::where('user_id', $id)->count('id', 'comments')->first()->comments;
    }

    public function countPresenceByUserID(int $id): Model
    {
        return Comment::where('user_id', $id)
            ->whereNull('parent_id')
            ->where(function ($query) {
                $query->where('is_admin', false)
                    ->whereNull('is_admin', 'OR');
            })
            ->groupBy('user_id')
            ->select([
                'SUM(CASE WHEN presence = TRUE THEN 1 ELSE 0 END) AS present_count',
                'SUM(CASE WHEN presence = FALSE THEN 1 ELSE 0 END) AS absent_count'
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
                'comments.is_admin',
                'comments.comment',
                'comments.gif_url',
                'comments.ip',
                'comments.user_agent',
                'comments.created_at',
                'comments.parent_id'
            ])
            ->select([
                'comments.uuid',
                'count(likes.id) as count_like',
                'comments.name',
                'comments.presence',
                'comments.is_admin',
                'comments.comment',
                'comments.gif_url',
                'comments.ip',
                'comments.user_agent',
                'comments.created_at as insert_at',
                'comments.parent_id'
            ])
            ->orderBy('insert_at', 'DESC')
            ->get();
    }
}
