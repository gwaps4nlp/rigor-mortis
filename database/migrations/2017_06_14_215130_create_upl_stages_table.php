<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\UplStage;

class CreateUplStagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upl_stages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label',100);
            $table->string('mode',50);
            $table->integer('stage');
            $table->text('description',2);
            $table->text('help');
            $table->timestamps();
        });
        UplStage::create([
            'label' => 'Intuition',
            'mode' => 'demo',
            'stage' => '0',
            'description' => "Phase d'intuition",
            'help' => "Phase d'intuition"
        ]); 
        UplStage::create([
            'label' => 'Training 1',
            'mode' => 'training',
            'stage' => '1',
            'description' => "Formation critère 1",
            'help' => "Aide formation 1"
        ]); 
        UplStage::create([
            'label' => 'Training 2',
            'stage' => '2',
            'mode' => 'training',            
            'description' => "Formation critère 2",
            'help' => "Aide formation 2"
        ]); 
        UplStage::create([
            'label' => 'Training 3',
            'stage' => '3',
            'mode' => 'training',
            'description' => "Formation critère 3",
            'help' => "Aide formation 3"
        ]); 
        UplStage::create([
            'label' => 'Training 4',
            'stage' => '4',
            'mode' => 'training',
            'description' => "Formation critère 4",
            'help' => "Aide formation 4"
        ]); 
        UplStage::create([
            'label' => 'Training 5',
            'stage' => '5',
            'mode' => 'training',
            'description' => "Formation critère 5",
            'help' => "Aide formation 5"
        ]);
        UplStage::create([
            'label' => 'Game',
            'stage' => '6',
            'mode' => 'game',
            'description' => "Trouve les expressions figées...",
            'help' => "Aide phase de jeu normal"
        ]);        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('upl_stages');
    }
}
