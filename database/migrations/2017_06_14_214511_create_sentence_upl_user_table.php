<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentenceUplUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sentence_upl_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('sentence_upl_id')->unsigned()->index();
            $table->integer('source_id')->unsigned();
            $table->integer('points')->unsigned()->default(0);
            $table->integer('points_not_seen')->unsigned()->default(0);
            $table->integer('bet')->unsigned()->nullable();
            $table->integer('experience')->unsigned()->nullable();
            $table->boolean('seen')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sentence_upl_user');
    }
}
