<?php

namespace App\Repositories;

use App\Models\UplStage;
use App\Models\SentenceUplUser;
use Gwaps4nlp\Core\Repositories\BaseRepository;
use DB;

class UplStageRepository extends BaseRepository 
{

	/**
	 * Create a new UplStageRepository instance.
	 *
	 * @param  App\UplStage $upl_stage
	 * @return void
	 */
	public function __construct(
		UplStage $upl_stage)
	{
		$this->model = $upl_stage;
	}
	
	/**
	 * Get a list of upl_stages
	 *
	 * @return Illuminate\Support\Collection [name => id]
	 */
	public function getList()
	{
		return $this->model->pluck('label','id');
	}

	/**
	 * Get the stages of a User
	 *
	 * @param  App\User $user
	 * @param  string $mode_stage (demo|training|game)
	 * @return Collection of App\UplStage
	 */
	public function getStagesByUser($user, $mode_stage)
	{
		$user_id = $user->id;

		return $this->model->selectRaw('upl_stages.*, ifnull(done,0) as done')
			->leftJoin('upl_stage_user',function($join) use ($user_id) {
            $join->on('upl_stage_id','=','upl_stages.id');
            $join->on('user_id','=',DB::raw($user_id));
        })->where('mode',$mode_stage)
          ->orderBy('stage')->get();

	}

	/**
	 * Get the stages of a User
	 *
	 * @param  App\User $user
	 * @param  string $mode_stage (demo|training|game)
	 * @return Collection of App\Models\UplStage
	 */
	public function getStageUserById($user, $id)
	{
		$user_id = $user->id;

		return $this->model->selectRaw('upl_stages.*, ifnull(done,0) as done')
			->leftJoin('upl_stage_user',function($join) use ($user_id) {
            $join->on('upl_stage_id','=','upl_stages.id');
            $join->on('user_id','=',DB::raw($user_id));
        })->where('upl_stages.id',$id)->firstOrFail();

	}

	/**
	 * Get the training stages not done by a given User
	 *
	 * @param  App\User $user	 
	 * @return Collection of App\Models\UplStage
	 */
	public function getTrainingStagesNotDone($user)
	{
		$user_id = $user->id;
		return $this->model->selectRaw('upl_stages.*, ifnull(done,0) done')->leftJoin('upl_stage_user',function($join) use ($user_id) {
				    $join->on('upl_stage_id','=','upl_stages.id');
				    $join->on('user_id','=',DB::raw($user_id));
				})->where('mode','training')->having('done','=',0)->get();

	}

	/**
	 * Count the number of training stages not done by a given user
	 *
	 * @param  App\User $user	 
	 * @return integer
	 */
	public function countTrainingStagesNotDone($user)
	{
		return $this->getTrainingStagesNotDone($user)->count();

	}

	/**
	 * Get the scores of upl game
	 *
	 * @return Collection
	 */
	public function getScores()
	{
		$fictive_user1 = SentenceUplUser::selectRaw(DB::raw('"Pr. Franckenperrier" as username, 0 as user_id, 2000 as score'));
		$fictive_user2 = SentenceUplUser::selectRaw(DB::raw('"Pr. Indianaperrier" as username, 0 as user_id, 10000 as score'));
		$scores = SentenceUplUser::select('username', 'user_id', DB::raw('SUM(points) as score'))
				->join('users','users.id','=','sentence_upl_user.user_id')
				->groupBy('user_id')
				->union($fictive_user1)
				->union($fictive_user2)
				->orderBy('score','desc')				
				->get();
		return $scores;
	}
}
