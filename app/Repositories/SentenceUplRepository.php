<?php

namespace App\Repositories;

use App\Models\SentenceUpl;
use Gwaps4nlp\Core\Repositories\BaseRepository;
use DB;

class SentenceUplRepository extends BaseRepository 
{

	/**
	 * Create a new SentenceUplRepository instance.
	 *
	 * @param  App\SentenceUpl $sentence_upl
	 * @return void
	 */
	public function __construct(
		SentenceUpl $sentence_upl)
	{
		$this->model = $sentence_upl;
	}
	
	/**
	 * Get a random mwe for playing
	 *
	 * @return App\Models\Mwe
	 */
	public function getRandom()
	{
		$sentence_upl=$this->model->orderBy(DB::raw('Rand()'))->first();
		return $sentence_upl;

	}
}
