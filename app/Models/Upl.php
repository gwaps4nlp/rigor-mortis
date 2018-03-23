<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upl extends Model
{
    protected $fillable = ['content','is_upl','identical_to'];

    public static function splitSentence($sentence){

		$words_sentence = preg_split("/[\s_]+/", $sentence);
		array_pop($words_sentence);

		for($index=0;$index<count($words_sentence)-1;$index++){

			$compound_word = explode("-", $words_sentence[$index]);
			if(count($compound_word)>1){
				$index_start = $index;
				$words_sentence[$index] = $compound_word[0];
				for($i=1;$i<count($compound_word);$i++){
					array_splice($words_sentence,$index_start+$i,0,'-');
					$index++;
					array_splice($words_sentence,$index_start+$i+1,0,$compound_word[$i]);
					$index_start++;
					$index++;
				}
			}
		}
		return $words_sentence;

    }


}
