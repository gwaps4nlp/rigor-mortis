<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUplsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upls', function (Blueprint $table) {
            $table->increments('id');
            $table->string('content',500);
            $table->boolean('is_upl')->nullable()->default(null);
            $table->integer('identical_to')->index();  
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
        Schema::drop('upls');
    }
}
