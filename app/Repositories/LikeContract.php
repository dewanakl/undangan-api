<?php

namespace App\Repositories;

use Core\Model\Model;

interface LikeContract
{
    public function create(int $user_id, string $comment_id): Model;
    public function getByUuid(int $user_id, string $uuid): Model;
    public function deleteByCommentID(string $uuid): int;
    public function countLikeByUserID(int $id): int;
}
