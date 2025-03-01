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
            $table->addColumn(function (Table $table) {

                $table->string('tenor_key', 100)->nullable();
            });
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
            $table->dropColumn('tenor_key');
        });
    }
};
