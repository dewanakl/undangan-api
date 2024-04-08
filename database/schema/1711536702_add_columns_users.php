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
                $table->boolean('can_edit')->nullable()->default(true);
                $table->boolean('can_delete')->nullable()->default(true);
                $table->boolean('can_reply')->nullable()->default(true);
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
            $table->dropColumn('can_edit');
            $table->dropColumn('can_delete');
            $table->dropColumn('can_reply');
        });
    }
};
