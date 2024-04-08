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
            $table->addColumn(function ($table) {

                $table->string('access_key', 50)->nullable()->unique();
                $table->boolean('is_filter')->nullable()->default(true);
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
            $table->dropColumn('access_key');
            $table->dropColumn('is_filter');
        });
    }
};
