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
        'is_active',
        'tenor_key',
        'is_confetti_animation',
        'tz',
    ];

    protected $casts = [
        'is_filter' => 'bool',
        'can_edit' => 'bool',
        'can_delete' => 'bool',
        'can_reply' => 'bool',
        'is_active' => 'bool',
        'is_confetti_animation' => 'bool',
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

    public function setAsAdmin(): void
    {
        $this->attributes['is_admin'] = true;
    }

    public function setAsNonAdmin(): void
    {
        $this->attributes['is_admin'] = false;
    }

    public function isAdmin(): bool
    {
        return boolval($this->attributes['is_admin']);
    }

    public function isActive(): bool
    {
        return boolval($this->attributes['is_active']);
    }

    public function isFilter(): bool
    {
        return boolval($this->attributes['is_filter']);
    }

    public function canReply(): bool
    {
        return boolval($this->attributes['can_reply']);
    }

    public function canEdit(): bool
    {
        return boolval($this->attributes['can_edit']);
    }

    public function canDelete(): bool
    {
        return boolval($this->attributes['can_delete']);
    }

    public function getTimezone(): string|null
    {
        return $this->attributes['tz'];
    }
}
