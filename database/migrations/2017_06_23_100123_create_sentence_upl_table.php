<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentenceUplTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sentence_upl', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sentence_id')->unsigned()->index();
            $table->integer('upl_id')->unsigned()->nullable()->default(null);
            $table->string('words_positions',200);        
            $table->integer('source_id')->unsigned();  
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sentence_upl');
    }
}
