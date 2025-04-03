<?php

namespace App\Providers;

use Core\Facades\Provider;
use Core\Valid\Trans;

class TranslatorServiceProvide extends Provider
{
    /**
     * Jalankan sewaktu aplikasi dinyalakan.
     *
     * @return void
     */
    public function booting()
    {
        $allowedLanguages = ['id', 'en'];
        $requestLang = strtolower(request()->get('lang', 'id'));

        if (!in_array($requestLang, $allowedLanguages, true)) {
            $requestLang = 'id';
        }

        Trans::setLanguage($requestLang);
    }
}
