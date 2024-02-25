<?php

use Core\Database\Migration;
use Core\Database\Schema;
use Core\Database\Table;

return new class implements Migration
{
    /**
     * Jalankan migrasi.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Table $table) {
            if ($table->checkColumn('nama')) {
                $table->renameColumn('nama', 'name');
            }
        });
    }

    /**
     * Kembalikan seperti semula.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Table $table) {
            if ($table->checkColumn('name')) {
                $table->renameColumn('name', 'nama');
            }
        });
    }
};
