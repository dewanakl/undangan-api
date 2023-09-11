<?php

/**
 * Import semua class yang digunakan dalam framework ini.
 * Tenang, ini telah dilakukan secara otomatis oleh composer.
 *
 * Sekarang, tinggal menjalankan aplikasi ini saja.
 */

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Okey, sekarang memanggil fungsi web secara static pada kernel.
 * Bentar, fungsi web ini perlu app kernel sebagai penghubung aplikasi.
 * Setelah itu, hanya perlu menjalankannya saja.
 *
 * Ini sangat simple.
 */

\Core\Kernel\Kernel::web(
    new \App\Kernel()
)->run();
