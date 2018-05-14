<?php

namespace App\Repositories;

use App\Models\UplStage;
use App\Models\SentenceUplStage;
use Gwaps4nlp\Core\Repositories\BaseRepository;
use DB;

class SentenceUplStageRepository extends BaseRepository 
{

	/**
	 * Create a new SentenceUplStageRepository instance.
	 *
	 * @param  App\SentenceUplStage $upl_stage
	 * @return void
	 */
	public function __construct(
		SentenceUplStage $sentence_upl_stage)
	{
		$this->model = $sentence_upl_stage;
	}

	/**
	 * Number of sentences already played by a user for a given stage
	 * 
	 * @param App\UplStage $stage
	 * @param App\User $user
	 * @return integer
	 */
	public function countDone($stage, $user)
	{
		return $this->model
					->select('sentence_id')
					->where('upl_stage_id',$stage->id)
					->whereExists( function($query) use($user) {
						$query->select(DB::raw(1))
	                      ->from('sentence_upl')
	                      ->join('sentence_upl_user','sentence_upl.id','=','sentence_upl_user.sentence_upl_id')
	                      ->where('sentence_upl_user.user_id','=',$user->id)
	                      ->whereRaw('sentence_upl.sentence_id = sentence_upl_stage.sentence_id');
					})->count();
	}

	/**
	 * Number of sentences not played by a user for a given stage
	 * 
	 * @param App\UplStage $stage
	 * @param App\User $user
	 * @return integer
	 */
	public function countNotDone($stage, $user)
	{
		return $this->model
					->select('sentence_id')
					->where('upl_stage_id',$stage->id)
					->whereNotExists( function($query) use($user) {
						$query->select(DB::raw(1))
		                  ->from('sentence_upl')
		                  ->join('sentence_upl_user','sentence_upl.id','=','sentence_upl_user.sentence_upl_id')
		                  ->where('sentence_upl_user.user_id','=',$user->id)
		                  ->whereRaw('sentence_upl.sentence_id = sentence_upl_stage.sentence_id');
					})->count();
	}

	/**
	 * Number of sentences of a given stage
	 * 
	 * @param App\User $user
	 * @return boolean
	 */
	public function count($stage)
	{
		return $this->model
					->select('sentence_id')
					->where('upl_stage_id',$stage->id)
					->count();		

	}
}
