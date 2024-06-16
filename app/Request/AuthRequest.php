<?php

namespace App\Request;

use Core\Valid\Form;

class AuthRequest extends Form
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'str', 'trim', 'min:5', 'max:30', 'email'],
            'password' => ['required', 'str', 'trim', 'min:8', 'max:20']
        ];
    }
}
