<?php

namespace App\Models;

use Gwaps4nlp\Core\Models\Sentence as Gwaps4nlpSentence;
use DB;

class Sentence extends Gwaps4nlpSentence
{

    /**
     * One to Many relation
     *
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function upls()
    {
        return $this->hasMany('App\Models\SentenceUpl');
    }

    /**
     * One to Many relation
     *
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function upls_ref()
    {
        return $this->hasMany('App\Models\SentenceUpl')->where('source_id',1);
    }

    /**
     * One to Many relation
     *
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function upls_user($user_id=null, $excluded_users=[])
    {
        if($user_id)
            return $this->hasMany('App\Models\SentenceUpl')
                    ->where('sentence_upl.source_id',2)
                    ->where('user_id',$user_id)
                    ->join('sentence_upl_user','sentence_upl.id','=','sentence_upl_id');
        elseif($excluded_users)
            return $this->hasMany('App\Models\SentenceUpl')
                    ->where('sentence_upl.source_id',2)
                    ->whereNotIn('user_id',$excluded_users)
                    ->join('sentence_upl_user','sentence_upl.id','=','sentence_upl_id');
        else
            return $this->hasMany('App\Models\SentenceUpl')
                ->where('sentence_upl.source_id',2)
                ->join('sentence_upl_user','sentence_upl.id','=','sentence_upl_id');
    }

    /**
     * One to Many relation
     *
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function count_upls_user($excluded_users=[])
    {
        return $this->upls_user(null, $excluded_users)
            ->selectRaw('sentence_id, upl_id, words_positions, sentence_upl.id as sentence_upl_id, count(*) as number')
            ->groupBy(DB::raw('sentence_id, words_positions'));
    }

    /**
     * One to Many relation
     *
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function count_distinct_players($users=[], $excluded_users=[])
    {
        if($users)
            return $this->upls_user()
                ->selectRaw('ifnull(count(DISTINCT user_id),0) as distinct_players')
                ->whereIn('user_id',$users)
                ->first()
                ->distinct_players;
        elseif($excluded_users)
            return $this->upls_user()
                ->selectRaw('ifnull(count(DISTINCT user_id),0) as distinct_players')
                ->whereNotIn('user_id',$excluded_users)
                ->first()
                ->distinct_players;
        else
            return $this->upls_user()
                ->selectRaw('ifnull(count(DISTINCT user_id),0) as distinct_players')
                ->first()
                ->distinct_players;
    }


    /**
     *
     * @return boolean
     */
    public function isReference()
    {
      return $this->source_id == Source::getReference()->id;
    }


}
