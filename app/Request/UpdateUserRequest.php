<?php

namespace App\Request;

use Core\Valid\Form;

class UpdateUserRequest extends Form
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'str', 'trim', 'max:50'],
            'old_password' => ['nullable', 'str', 'trim', 'max:50'],
            'new_password' => ['nullable', 'str', 'trim', 'max:50']
        ];
    }
}
