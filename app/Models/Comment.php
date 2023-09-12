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
            ->with($this->likes());
    }

    public function likes(): \Core\Model\Relational
    {
        return $this->hasMany(Like::class, 'comment_id', 'uuid', function (\Core\Model\Query $query): \Core\Model\Query {
            return $query->select('created_at')->orderBy('created_at', 'DESC');
        })->as('likes');
    }
}
