<?php

return [
    /**
     * AuthManager.
     */
    \Core\Auth\AuthManager::NAME => [

        'failed' => ':email or :password incorrect.',
    ],

    /**
     * Time.
     */
    \Core\Support\Time::NAME => [

        'y' => 'year',
        'm' => 'month',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',

        'ago' => 'ago.',
        'recently' => 'just now.',
    ],

    /**
     * Validator.
     */
    \Core\Valid\Validator::NAME => [

        'request' => [
            'required' => ':field is required!.',
            'email' => ':field is invalid!.',
            'dns' => ':field is invalid!.',
            'url' => ':field is invalid!.',
            'uuid' => ':field is not a uuid!.',
            'int' => ':field must be a number!.',
            'float' => ':field must be decimal!.',
            'min' => ':field minimum length: :attribute',
            'max' => ':field maximum length: :attribute',
            'sama' => ':field does not match with :attribute',
            'unik' => ':field already exists!.',
        ],

        'file' => [
            'required' => ':field is required!.',
            'min' => ':field minimum length: :attribute',
            'max' => ':field maximum length: :attribute',
            'mimetypes' => ':field allowed: :attribute',
            'mimes' => ':field allowed: :attribute',
            'unsafe' => ':field is indicated as unsafe!.',
            'corrupt' => ':field was not uploaded correctly!.',
        ],
    ]
];
