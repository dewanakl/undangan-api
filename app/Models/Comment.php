<?php

namespace App\Models;

use Core\Model\Model;
use Core\Model\Query;
use Core\Model\Relational;

final class Comment extends Model
{
    protected $table = 'comments';

    protected $casts = [
        'created_at' => 'datetime:diff'
    ];

    public function comments(): Relational
    {
        return $this->hasMany(
            Comment::class,
            'parent_id',
            'uuid',
            function (Query $query): Query {
                return $query->select(['uuid', 'nama', 'hadir', 'komentar', 'created_at'])->orderBy('id');
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
