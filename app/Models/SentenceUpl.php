<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SentenceUpl extends Model
{
    protected $fillable = ['words_positions','sentence_id','upl_id','source_id','known_upl','is_upl'];
    
    protected $table = 'sentence_upl';
    
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function sentence_upl_users()
    {
        return $this->hasMany('App\Models\SentenceUplUser');
    }

    public function upl()
    {
        return $this->belongsTo('App\Models\Upl');
    }

}
