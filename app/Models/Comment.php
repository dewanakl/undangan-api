<?php

namespace App\Models;

use Core\Model\Model;
use Core\Model\Query;
use Core\Model\Relational;

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
    ];

    protected $casts = [
        'presence' => 'bool',
        'is_admin' => 'bool',
        'created_at' => 'datetime:diff'
    ];

    public function comments(): Relational
    {
        return $this->hasMany(
            Comment::class,
            'parent_id',
            'uuid',
            function (Query $query): Query {
                return $query->select(['uuid', 'name', 'presence', 'comment', 'is_admin', 'created_at', ...(!empty(auth()->user()->is_admin) ? ['ip', 'own', 'user_agent'] : [])])->orderBy('id');
            }
        )->as('comments')->with($this->likes())->recursive();
    }

    public function likes(): Relational
    {
        return $this->belongsTo(
            Like::class,
            'uuid',
            'comment_id',
            function (Query $query): Query {
                return $query->count('uuid', 'love');
            }
        )->as('like');
    }
}
