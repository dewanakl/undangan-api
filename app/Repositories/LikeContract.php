<?php

namespace App\Repositories;

use Core\Model\Model;

interface LikeContract
{
    public function create(int $userid, string $commentid): Model;
    public function getByUuid(int $userid, string $uuid): Model;
    public function deleteByCommentID(string $uuid): int;
}
