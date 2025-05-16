<?php

namespace App\Repositories;

use App\Models\User;
use Core\Valid\Hash;

class UserRepositories implements UserContract
{
    public function generateNewAccessKey(int $user_id): int
    {
        $generateUnique = static function () use (&$generateUnique): string {
            $key = Hash::rand(25);

            if (User::where('access_key', $key)->first()->exist()) {
                return $generateUnique();
            }

            return $key;
        };

        $newKey = $generateUnique();

        return User::where('id', $user_id)->update([
            'access_key' => $newKey
        ]);
    }
}
