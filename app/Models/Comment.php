<?php

namespace App\Models;

use Core\Model\Model;

final class Comment extends Model
{
    protected $table = 'comments';

    protected $casts = [
        'created_at' => 'datetime:diff'
    ];

    public function comments(): \Core\Model\Relational
    {
        return $this->hasMany(
            static::class,
            'parent_id',
            'uuid',
            function (\Core\Model\Query $query): \Core\Model\Query {
                return $query->select(['uuid', 'nama', 'hadir', 'komentar', 'created_at'])->orderBy('id');
            }
        )
            ->recursive()
            ->with($this->likes())
            ->as('comments');
    }

    public function likes(): \Core\Model\Relational
    {
        return $this->belongsTo(Like::class, 'uuid', 'comment_id', function (\Core\Model\Query $query): \Core\Model\Query {
            return $query->count('uuid', 'love');
        })->as('like');
    }
}
