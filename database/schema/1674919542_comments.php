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
        Schema::create('comments', function (Table $table) {
            $table->id();

            $table->integer('user_id');

            $table->string('nama');
            $table->boolean('hadir')->default(false);
            $table->text('komentar')->nullable();

            $table->foreign('user_id')->references('id')->on('users');

            $table->timeStamp();
        });
    }

    /**
     * Kembalikan seperti semula.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('comments');
    }
};
