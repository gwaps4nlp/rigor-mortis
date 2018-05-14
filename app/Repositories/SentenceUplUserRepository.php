<?php

namespace App\Repositories;

use App\Models\SentenceUplUser;
use Gwaps4nlp\Core\Repositories\BaseRepository;
use DB;

class SentenceUplUserRepository extends BaseRepository 
{

	/**
	 * Create a new SentenceUplUserRepository instance.
	 *
	 * @param  App\SentenceUplUser $sentence_upl_user
	 * @return void
	 */
	public function __construct(
		SentenceUplUser $sentence_upl_user)
	{
		$this->model = $sentence_upl_user;
	}

	
	/**
	 * Get the upls not seen for a given user
	 * 
	 * @param App\Models\User $user
	 * @return Collection of SentenceUplUser
	 */
	public function getNotSeen($user)
	{
		$query = $this->model->where('user_id',$user->id)->where('seen',0);

		return $query->get();
	}

	/**
	 * Reset the upls not seen for a given user
	 * 
	 * @param App\Models\User $user
	 * @return boolean
	 */
	public function resetNotSeen($user)
	{
		return $this->model->where('user_id',$user->id)->where('seen',0)->update(['seen'=>1,'points_not_seen'=>0]);

	}

}
