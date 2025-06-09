<?php

namespace App\Models;

use Core\Model\Model;

final class Comment extends Model
{
    protected $table = 'comments';

    protected $fillable = [
        'user_id',
        'name',
        'presence',
        'comment',
        'uuid',
        'ip',
        'user_agent',
        'parent_id',
        'own',
        'is_admin',
        'gif_url',
    ];

    protected $casts = [
        'presence' => 'bool',
        'is_admin' => 'bool',
        'created_at' => 'datetime:diff'
    ];
}
