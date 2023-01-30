<?php

namespace App\Models;

use Core\Model\Model;

final class User extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
