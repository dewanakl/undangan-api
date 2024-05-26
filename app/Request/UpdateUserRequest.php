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
            'name' => ['nullable', 'str', 'trim', 'min:1', 'max:40'],
            'old_password' => ['nullable', 'str', 'trim', 'min:8', 'max:20'],
            'new_password' => ['nullable', 'str', 'trim', 'min:8', 'max:20']
        ];
    }
}
