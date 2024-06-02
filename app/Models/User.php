<?php

namespace App\Models;

use Core\Model\Model;
use Core\Valid\Hash;

final class User extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $typeKey = 'int';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'access_key',
        'is_filter',
        'can_edit',
        'can_delete',
        'can_reply',
    ];

    protected $casts = [
        'is_filter' => 'bool',
        'can_edit' => 'bool',
        'can_delete' => 'bool',
        'can_reply' => 'bool',
    ];

    public function __construct(array $data = [])
    {
        $this->fill($data);
    }

    protected function fakes(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => Hash::make(fake()->text(8)),
        ];
    }
}
