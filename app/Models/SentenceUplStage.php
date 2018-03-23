<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentenceUplStage extends Model
{

	protected $table = 'sentence_upl_stage';

    protected $fillable = ['upl_stage_id','sentence_id','stage_order'];

}
