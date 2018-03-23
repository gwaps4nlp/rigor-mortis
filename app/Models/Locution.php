<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Sofa\Eloquence\Eloquence;
use DB;

class Locution extends Model
{
	// use Eloquence;

	protected $fillable = ['expression','description'];
	protected $searchableColumns = ['expression'];

	static function getExpression($expression){

		 // $result = Locution::search($expression)
		 // ->where('expression','LIKE',$expression)->first();
		 $result = Locution::where('expression','LIKE',"{$expression}")->first();

		 return $result;
	}	

}
