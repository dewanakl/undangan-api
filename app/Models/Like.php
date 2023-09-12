<?php

namespace App\Models;

use Core\Model\Model;

final class Like extends Model
{
    protected $table = 'likes';

    protected $casts = [
        'created_at' => 'datetime:diff'
    ];
}
