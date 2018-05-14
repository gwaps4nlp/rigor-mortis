<?php

namespace App\Repositories;

use Gwaps4nlp\Core\Models\Corpus;
use Gwaps4nlp\Core\Models\Source;
use Gwaps4nlp\Core\Repositories\BaseRepository;
use DB;
use Config;

class CorpusRepository extends BaseRepository
{

	/**
	 * Create a new RelationRepository instance.
	 *
	 * @param  App\Models\Relation $relation
	 * @return void
	 */
	public function __construct(
		Corpus $corpus)
	{
		$this->model = $corpus;
	}

	/**
	 * 
	 *
	 * @return App\Models\Corpus
	 */
	public function create($inputs)
	{
		return $this->model->create($inputs);
	}
	
	/**
	 * Get a list of corpora
	 *
	 * @return Illuminate\Support\Collection [name => id]
	 */
	public function getList()
	{
		return $this->model->pluck('name','id');
	}

	/**
	 * Get a list of reference corpora
	 *
	 * @return Illuminate\Support\Collection [name => id]
	 */
	public function getListReference()
	{
		return $this->model->where('source_id','=',Source::getReference()->id)->pluck('name','id');
	}

	/**
	 * Get a list of pre-annotated corpora
	 *
	 * @return Illuminate\Support\Collection [name => id]
	 */
	public function getListPreAnnotated()
	{
		return $this->model->where('source_id','=',Source::getPreAnnotated()->id)->pluck('name','id');
	}

	/**
	 * Get a list of pre-annotated corpora
	 *
	 * @return Illuminate\Support\Collection [name => id]
	 */
	public function getListPlayable()
	{
		return $this->model->where('playable','=',1)->pluck('name','id');
	}


	/**
	 * Get a list of evaluation corpora
	 *
	 * @return Illuminate\Support\Collection [name => id]
	 */
	public function getListEvaluation()
	{
		return $this->model->where('source_id','=',Source::getPreAnnotatedForEvaluation()->id)->pluck('name','id');
	}

	/**
	 * Get a collection of evaluation corpora
	 *
	 * @return Collection of App\Models\Corpus
	 */
	public function getEvaluation()
	{
		return $this->model->where('source_id','=',Source::getPreAnnotatedForEvaluation()->id)->get();
	}	

}
