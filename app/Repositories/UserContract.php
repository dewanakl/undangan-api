<?php

namespace App\Repositories;

interface UserContract
{
    public function generateNewAccessKey(int $user_id): int;
}
