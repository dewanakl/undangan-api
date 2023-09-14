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
        Schema::create('likes', function (Table $table) {
            $table->id();

            $table->string('uuid');
            $table->string('comment_id');

            $table->foreign('comment_id')->references('uuid')->on('comments');

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
        Schema::drop('likes');
    }
};
