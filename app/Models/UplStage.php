<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UplStage extends Model
{
    protected $fillable = ['label','mode','stage','description','help'];
    
	/**
	 * Many to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\belongToMany
	 */
	public function playable_sentences()
	{
		return $this->belongsToMany('App\Models\Sentence')
			->whereNull('stage_order')->orderBy('sentence_id');
	}
	/**
	 * Many to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\belongToMany
	 */
	public function reference_sentences()
	{
		return $this->belongsToMany('App\Models\Sentence')
			->whereNotNull('stage_order')
			->orderBy('stage_order');
	}

	/**
	 * Many to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\belongToMany
	 */
	public function sentences()
	{
		return $this->belongsToMany('App\Models\Sentence');
	}
}
