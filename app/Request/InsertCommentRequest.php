<?php

namespace App\Request;

use Core\Valid\Form;

class InsertCommentRequest extends Form
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['nullable', 'str', 'trim', 'uuid', 'max:37'],
            'nama' => ['required', 'str', 'trim', 'max:50'],
            'hadir' => ['bool'],
            'komentar' => ['required', 'str', 'max:500'],
            'ip' => ['nullable', 'str', 'trim', 'max:50'],
            'user_agent' => ['nullable', 'str', 'trim', 'max:500']
        ];
    }
}
