<?php

namespace App\Repositories;

use App\Models\Comment;
use Core\Model\Model;
use Ramsey\Uuid\Uuid;
use stdClass;

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
         * @param Comment $comments
         * @return array
         */
        $buildTree = function ($comments) use (&$buildTree, $selectedFields, $user_id, $user_name): array {
            $uuids = [];
            foreach ($comments as $comment) {
                $uuids[] = $comment->uuid;
            }

            $grouped = [];
            Comment::leftJoin('likes', 'comments.uuid', 'likes.comment_id')
                ->whereIn('comments.parent_id', $uuids)
                ->where('comments.user_id', $user_id)
                ->select($selectedFields)
                ->select(['comments.id', 'false as is_parent', 'comments.parent_id', 'count(likes.id) as like'])
                ->groupBy(['comments.id', ...$selectedFields])
                ->orderBy('comments.id', 'DESC')
                ->get()
                ->map(function ($child) use (&$grouped): void {
                    $grouped[$child->parent_id][] = $child;
                });

            $result = [];
            foreach ($comments as &$comment) {
                $comment->comments = isset($grouped[$comment->uuid]) ? $buildTree($grouped[$comment->uuid]) : [];

                if ($comment->is_admin) {
                    $comment->name = $user_name;
                }

                // this change is backward-compatible
                $love = new stdClass();
                $love->love = $comment->like;
                $comment->like = $love;

                unset($comment->id);
                unset($comment->parent_id);
                $result[] = $comment;
            }

            return $result;
        };

        $parents = Comment::leftJoin('likes', 'comments.uuid', 'likes.comment_id')
            ->whereNull('comments.parent_id')
            ->where('comments.user_id', $user_id)
            ->select($selectedFields)
            ->select(['comments.id', 'true as is_parent', 'comments.parent_id', 'count(likes.id) as like'])
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
                'comments.created_at as is_created',
                'comments.parent_id'
            ])
            ->orderBy('is_created', 'DESC')
            ->get();
    }

    public function getByUuidWithoutUser(string $uuid): Model
    {
        return Comment::where('uuid', $uuid)->first();
    }
}
