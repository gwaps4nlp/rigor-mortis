<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentenceUplStageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sentence_upl_stage', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('upl_stage_id')->unsigned()->index();
            $table->integer('sentence_id')->unsigned();
            $table->integer('stage_order')->default(null)->nullable()->unsigned();
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
        Schema::drop('sentence_upl_stage');
    }
}
