<?php

namespace App\Models;

use Core\Model\Model;

final class Like extends Model
{
    protected $table = 'likes';

    protected $fillable = [
        'uuid',
        'comment_id',
        'user_id',
    ];
}
