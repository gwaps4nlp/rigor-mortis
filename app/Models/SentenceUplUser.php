<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentenceUplUser extends Model
{
    protected $fillable = ['user_id','sentence_upl_id','source_id','points','seen'];
    
    protected $table = 'sentence_upl_user';
}
