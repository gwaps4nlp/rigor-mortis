<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Dela extends Model
{
	// use Eloquence;

	protected $fillable = ['expression','description','lemma'];
	protected $table = 'dela-fr';
	protected $searchableColumns = ['expression'];

	static function getExpression($expression){

		 // $result = Dela::search($expression)
		 // ->where('expression','LIKE',$expression)
		 // ->first();

		 $result = Dela::where('expression','LIKE',"{$expression}")
		 ->first();
		 return $result;
	}	

	static function getLemma($expression){

		// $result = Dela::search($expression)
		// ->where('expression','LIKE',$expression)
		// ->where('lemma','!=','')
		// ->get();
		$result = Dela::where('expression','LIKE',"{$expression}")
		->where('lemma','!=','')
		->get();

		return $result;
	}
	
	static function getLemmaVerb($expression){

		// $result = Dela::selectRaw(DB::raw('expression, lemma'))
		// ->search($expression)
		// ->where('expression','LIKE',$expression)
		// ->where('lemma','!=','')
		// ->where('description','LIKE','V%')
		// ->get();
		$result = Dela::selectRaw(DB::raw('expression, lemma'))
		->where('expression','LIKE',"{$expression}")
		->where('lemma','!=','')
		->where('description','LIKE','V%')
		->get();		

		return $result;
	}

}
