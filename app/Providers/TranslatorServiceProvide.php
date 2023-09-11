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
        Trans::setLanguage('id');
    }
}
