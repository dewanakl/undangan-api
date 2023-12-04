<?php

namespace App\Repositories;

use Core\Model\Model;

interface CommentContract
{
    public function create(array $data): Model;
    public function getAll(int $userid, int $limit, int $offset): Model;
    public function getByUuid(int $userid, string $uuid): Model;
    public function getByOwnid(int $userid, string $ownid): Model;
    public function deleteByParrentID(string $uuid): int;
}
