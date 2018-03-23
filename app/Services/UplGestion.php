<?php

namespace App\Services;


use Illuminate\Http\Request;

use App\Models\UplStage;
use App\Models\Upl;
use App\Models\SentenceUpl;
use App\Models\SentenceUplUser;
use App\Models\UplStageUser;
use App\Models\SentenceUplStage;
use App\Models\Sentence;
use App\Models\Locution;
use App\Models\Dela;
use App\Exceptions\UplGameException;
use Gwaps4nlp\Models\ConstantGame;
use Gwaps4nlp\Models\Source;
use Gwaps4nlp\Game;
use App\Repositories\UplStageRepository;
use App\Repositories\SentenceUplStageRepository;
use Response, View, DB;

class UplGestion extends Game 
{
	
	public $mode = 'upl';

    /**
     * Attributes stored in session
     */
	protected $fillable = ['turn', 'nb_turns', 'sentence', 'stage', 'mode_stage', 'stage_user', 'sentences_ids', 'sentence_id', 'correct_answers', 'expected_answers', 'number_upls', 'score', 'reference_again', 'theme'];
    /**
     * Attributes visible in serialization of UplGestion
     */
	protected $visible = ['turn', 'nb_turns','sentence','html','stage_user','correct_upls','mode_stage','next_stage','trophies', 'score', 'theme' ];
	
	public function __construct(Request $request, UplStageRepository $upl_stages, SentenceUplStageRepository $sentence_upl_stages){

		parent::__construct($request);
		$this->upl_stages = $upl_stages;
		$this->sentence_upl_stages = $sentence_upl_stages;
	
	}

	public function begin(Request $request, $stage_id){
        $this->loadSession($request);	
		if($this->request->has('admin-out')){
			$this->set('mode_stage',null);
			return json_encode(['href'=>url('upl/admin-index')]);
		}

		$stage = UplStage::find($stage_id);
		
		if(!$stage)
			throw new UplGameException("Niveau inconnu.");

		if($this->request->session()->get('upl.status')=='expert'){
			$this->set('mode_stage','expert');
		}
		elseif($this->request->session()->get('upl.status')=='admin'){
			$this->set('mode_stage','admin');
		}
		else {
			$this->set('mode_stage',$stage->mode);
		}

		if(!$this->request->ajax() && $this->request->has('expert') && $this->user->isAdmin()){
			$this->set('mode_stage', 'expert');
			$this->request->session()->flash('upl.status', 'expert');
		}
		if(!$this->request->ajax() && $this->request->has('admin') && $this->user->isAdmin()){
			$this->set('mode_stage', 'admin');
			$this->request->session()->flash('upl.status', 'admin');
		}

		$this->set('score', SentenceUplUser::where('user_id', $this->user->id)->sum('points'));

		$stage_user = UplStageUser::firstOrCreate(['upl_stage_id'=>$stage_id, 'user_id' => $this->user->id]);

		if(!in_array($this->mode_stage,['expert','admin'])){
			if($stage->mode=='demo' && $stage_user->done==1)
				throw new UplGameException("Tu as déjà fait la phase d'inititation !");
			if($stage->mode=='training'){
				$user_id = $this->user->id;
				$demo_stage = $this->upl_stages->getStagesByUser($this->user, 'demo')->first();

		        if(!$demo_stage->done)
					throw new UplGameException("Tu dois déjà faire la phase d'inititation !");

			}
			if($stage->mode=='game'){
		        if($this->upl_stages->countTrainingStagesNotDone($this->user) > 0)
					throw new UplGameException("Tu dois déjà faire toutes les formations !");

		        if($this->upl_stages->countTrainingStagesNotDone($this->user) > 0)
					throw new UplGameException("Tu dois déjà faire toutes les formations !");

				// bonus stage
				if($stage_id==8){
			        $game_stage = UplStage::find(7);
			        $count_sentences_not_done = $this->sentence_upl_stages->countNotDone($game_stage, $this->user);
			    	if($count_sentences_not_done>0)
			    		throw new UplGameException("Tu dois déjà faire la 3e phase !");
				}

			}			
		}

		if($stage->mode=='demo' || $stage->mode=='training' ) {
			// Remove previous answers
			$upl_stage_id = $stage->id;
			SentenceUplUser::whereExists(function ($query) use ($upl_stage_id) {
                $query->select(DB::raw(1))
                      ->from(DB::raw('sentence_upl, sentence_upl_stage'))
                      ->whereRaw('sentence_upl_stage.sentence_id = sentence_upl.sentence_id')
                      ->whereRaw('sentence_upl.id = sentence_upl_user.sentence_upl_id')
                      ->where('upl_stage_id',$upl_stage_id);
            })
            ->where('user_id',$this->user->id)
            ->delete();

			$sentences_ids = SentenceUplStage::select('sentence_id')->where('upl_stage_id',$stage->id)->orderBy('stage_order')->get()->pluck('sentence_id');

			$this->set('sentences_ids',$sentences_ids->toArray());
			$this->set('nb_turns',$sentences_ids->count());
		} else {
			
			$user_id = $this->user->id;
			$sentences_ids = SentenceUplStage::select('sentence_id')
				->where('upl_stage_id',$stage->id)
				->orderBy('sentence_id');

			if(in_array($this->mode_stage,['expert','admin']))
				$sentences_ids = $sentences_ids->whereNotNull('stage_order');
			else
				$sentences_ids = $sentences_ids->whereNotExists( function($query) use($user_id) {
					$query->select(DB::raw(1))
                      ->from('sentence_upl')
                      ->join('sentence_upl_user','sentence_upl.id','=','sentence_upl_user.sentence_upl_id')
                      ->where('sentence_upl_user.user_id','=',$user_id)
                      ->whereRaw('sentence_upl.sentence_id = sentence_upl_stage.sentence_id');
				})->whereNull('stage_order')->limit(10);

			$sentences_ids = $sentences_ids->get()->pluck('sentence_id');

			if(!in_array($this->mode_stage,['expert','admin'])){
				if(rand(0,100)>50){
					$sentence_reference_id = SentenceUplStage::select('sentence_id')
						->where('upl_stage_id',$stage->id)
						->whereNotNull('stage_order')
						->whereNotExists( function($query) use($user_id) {
							$query->select(DB::raw(1))
		                      ->from('sentence_upl')
		                      ->join('sentence_upl_user','sentence_upl.id','=','sentence_upl_user.sentence_upl_id')
		                      ->where('sentence_upl_user.user_id','=',$user_id)
		                      ->whereRaw('sentence_upl.sentence_id = sentence_upl_stage.sentence_id');
						})
						->first();
					if($sentence_reference_id){
						$sentences_ids[min(rand(0,3),$sentences_ids->count()-1)] = $sentence_reference_id->sentence_id;
					}
				}
			}
			
			$this->set('sentences_ids',$sentences_ids->toArray());
			$this->set('nb_turns',$sentences_ids->count());
		}
		
		$this->set('correct_answers',0);
		// for mode=demo or mode =training : number_upls is the number of upls a player should find.
		$this->set('number_upls',0);
		$this->set('stage',$stage);
		$this->set('stage_user',$stage_user);
		$this->set('theme',$this->getTheme());
		$this->set('turn',0);
		
	}

	public function loadContent(){
		$this->set('sentence',$this->getSentence());
		if($this->mode_stage=='expert')
			$this->correct_upls = SentenceUpl::select('words_positions')->where('sentence_id',$this->sentence->id)->where('source_id',Source::getReference()->id)->get();
	}

	private function getSentence(){
		if($this->reference_again){
			$sentence_id = SentenceUplStage::join('upl_stages','upl_stage_id','=','upl_stages.id')->select('sentence_id')
			->where('mode','=','training')->orderBy(DB::raw('Rand()'))->first()->sentence_id;
			$this->set('reference_again',0);
		} else {
			$sentence_id = $this->sentences_ids[$this->turn];
		}
		$this->set('sentence_id',$sentence_id);
		$sentence = Sentence::findOrFail($sentence_id);
		return $sentence;
	}

	public function jsonAnswer(Request $request){
		$this->loadSession($request);		
		$this->processAnswer();
        $reponse = array(
			'nb_turns' => $this->nb_turns,
			'turn' => $this->turn,
		);
        if($this->stage->mode=='game'){
        	$this->set('score', SentenceUplUser::where('user_id', $this->user->id)->sum('points'));
        	$reponse['score'] = $this->score;
        	$reponse['experience'] = $this->stage_user->experience;
        	$reponse['money'] = $this->stage_user->money;
        	$reponse['samples'] = $this->stage_user->samples;
        }
        if($this->correct_upls)
        	$reponse['correct_upls'] = $this->correct_upls;
        if($this->mode_stage=='prototype'){
	        if($this->unknown_upls)
	        	$reponse['unknown_upls'] = $this->unknown_upls;
	        if($this->likely_upls)
	        	$reponse['likely_upls'] = $this->likely_upls;
	    }
        return Response::json($reponse);
	}
	
	public function end(){

		if(!in_array($this->mode_stage,['expert','admin','game'])){
			$this->stage_user->done = 1;
			$this->stage_user->save();
		}

		$number_upls = SentenceUplUser::where('user_id',$this->user->id)->where('points',50)->count();

		$this->checkTrophy('number_upls', $number_upls);
		
		$training_stages_not_done = $this->upl_stages->getTrainingStagesNotDone($this->user);

		if($this->stage->mode=='training'){
			
			if($training_stages_not_done->count()==0)
				$next_stage = $this->upl_stages->getStagesByUser($this->user,'game')->first();
			else
				$next_stage = $training_stages_not_done->first();

		} elseif($this->stage->mode=='demo'){
			$next_stage = $training_stages_not_done->first();
		} else {
			$user_id = $this->user->id;
			$this->number_sentences_to_do = $this->sentence_upl_stages->countNotDone($this->stage, $this->user);
			$this->number_sentences_stage = $this->sentence_upl_stages->count($this->stage);
			$next_stage = null;
		}
		
		$this->set('next_stage', $next_stage);

	}
	
	public function processAnswer(){

		if($this->sentence_id!=$this->request->input('sentence_id'))
			throw new UplGameException("Cette partie a expiré. <a href=''>retour à l'index</a>");

		$upls_user = $this->request->input('upls');

		// get the answers considered as reference (annotated by an expert)
		$correct_upls = SentenceUpl::select('words_positions')->where('sentence_id',$this->sentence_id)->where('source_id',Source::getReference()->id)->get();
		
		if($this->stage->mode!='demo' && $this->mode_stage!='expert')
			$this->correct_upls = $correct_upls;

		$this->increment('number_upls',$correct_upls->count());
		
		if($this->mode_stage=='expert'){
			SentenceUpl::where('sentence_id',$this->sentence_id)->where('source_id',Source::getReference()->id)->delete();
		}
		if(!is_array($upls_user))
			$upls_user[0] = array('words_positions'=>'0');

		$unknown_upls = [];
		$likely_upls = [];
		// Split the sentence in tokkens
		$words_sentence = preg_split("/[\s_]+/", $this->sentence->content);
		for($index=0;$index<count($words_sentence);$index++){

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
		
		foreach($upls_user as $key=>$words){
			$upl_words = [];
			$upl_id = null;
			$sentence_upl = null;
			$points = 0;
			$words_sort = collect($words['words_positions'])->unique()->sort();
			$words_positions = $words_sort->implode('-');
			$locution = null;
			if($words_sort->count()>1){
				
				foreach($words_sort as $word_position){
					$index = intval($word_position)-1;
					if(isset($words_sentence[$index])){
						$upl_words[]=$words_sentence[$index];
					}
				}
				$upl_str = join(' ',$upl_words);
				$upl_str = str_replace(' - ','-',$upl_str);
				$upl_str = str_replace("' ","'",$upl_str);
				$upl = Upl::firstOrCreate(['content'=>$upl_str]);
				$upl_id = $upl->id;
				if($this->stage->mode=='game'){

					//Expression already played by another player ?
					$sentence_upl = SentenceUpl::where('words_positions',$words_positions)->where('sentence_id',$this->sentence_id)->first();

					if($sentence_upl && $sentence_upl->known_upl==1){
						$points = 50;
						$this->stage_user->increment('experience');
						$this->stage_user->increment('money');
					} elseif($sentence_upl && $sentence_upl->known_upl===0){
						$count_same_answer = SentenceUplUser::where('sentence_upl_id',$sentence_upl->id)->where('user_id','!=',$this->user->id)->count();
						if($count_same_answer==0) {
							$points = 5;
						} elseif($count_same_answer==1) {
							$points = 25;
							$points_not_seen = 20;
							SentenceUplUser::where('sentence_upl_id',$sentence_upl->id)
											->update(['points' => $points,'points_not_seen' => DB::raw('points_not_seen+'.$points_not_seen),'seen' => 0]);
						} elseif($count_same_answer==2) {
							$points = 50;
							$points_not_seen = 25;
							SentenceUplUser::where('sentence_upl_id',$sentence_upl->id)
											->update(['points' => $points,'points_not_seen' => DB::raw('points_not_seen+'.$points_not_seen),'seen' => 0]);
						} elseif($count_same_answer>2){
							$points = 50;
						}
						// $this->stage_user->increment('experience');
						// $this->stage_user->increment('money');
					} elseif($sentence_upl){
						$likely_upls[] = $words_positions;
					}
					//Expression not already played by another player => is it a known upl ?
					elseif($locution = $this->getLocution($upl_str)){
						$points = 50;
						$this->stage_user->increment('experience');
						$this->stage_user->increment('money');
					} elseif($locution = $this->getDerivedLocution($upl_words)) {
						$points = 50;
						$this->stage_user->increment('experience');
						$this->stage_user->increment('money');
					} else {
						$points = 5;
						$unknown_upls[] = $words_positions;
						// $this->stage_user->increment('experience');
					}

					$this->stage_user->increment('score',$points);

				}

			} else {
				$upl = Upl::firstOrCreate(['content'=>""]);
				$upl_id = $upl->id;					
			}

			// mode expert => the answers are recorded as references
			if($this->mode_stage=='expert'){
				$sentence_upl = SentenceUpl::where([
					'words_positions'=>$words_positions,
					'sentence_id'=>$this->request->input('sentence_id'),
					'upl_id'=>$upl_id,
					'source_id'=>Source::getReference()->id,
				])->withTrashed()->first();
				if($sentence_upl) $sentence_upl->restore();
				else 
					$sentence_upl = SentenceUpl::create([
					'words_positions'=>$words_positions,
					'sentence_id'=>$this->request->input('sentence_id'),
					'upl_id'=>$upl_id,						
					'source_id'=>Source::getReference()->id,
				]);
			} elseif($this->mode_stage=='admin'){
				// test mode => the answers are not recorded
			} else {
				
				$correct_answer_found = false;

				foreach($correct_upls as $correct_upl){
					$correct_upl = collect(explode('-',$correct_upl->words_positions));
					
					$upl_user = collect($words['words_positions']);

					$intersect = $upl_user->intersect($correct_upl);
					if(
						($correct_upl->count()==2 && $intersect->count()==2)
						|| 
						($correct_upl->count()>2 && $intersect->count()>=($correct_upl->count()-1) && $upl_user->count()<=($correct_upl->count()+1)
						)){
						$this->increment('correct_answers');
						$correct_answer_found = true;
						break;
					}
				}
				if($this->mode_stage=='game' && $correct_upls->count()>0 && !$correct_answer_found){
					$this->set('reference_again',1);
				}
				// 
				$known_upl = ($locution)?1:0;
				if(!$sentence_upl)
					$sentence_upl = SentenceUpl::firstOrCreate([
						'words_positions'=>$words_positions,
						'sentence_id'=>$this->sentence_id,
						'upl_id'=>$upl_id,						
						'source_id'=>Source::getUser()->id,
						'known_upl'=>$known_upl,
					]);
				
				if(in_array($this->mode_stage,['demo','training']))
					$source_id = Source::getTraining()->id;
				else
					$source_id = Source::getUser()->id;

				//do not give points if a bad answer is given to a sentence of control
				if($this->reference_again)
					$points = 0;

				$upl_user = SentenceUplUser::firstOrCreate(['user_id'=>$this->user->id, 'sentence_upl_id' => $sentence_upl->id, 'source_id' => $source_id, 'points' => $points, 'seen' => 1 ]);
			}

		}
		if($this->stage->mode=="game"){
			$this->unknown_upls = $unknown_upls;
			$this->likely_upls = $likely_upls;
		}
		// } else {
		// 	throw new UplGameException("Tu dois fournir au moins une réponse !");
		// }
		$this->set('sentence_id',null);
		if(!$this->reference_again)
			$this->incrementTurn();
		
	}
	
	private function getDerivedLocution($upl_words){

		$similar_upl_words = [];

		$index_derived_upl = 0;

		// Replacement of the conjugated verbs by its lemmas
		foreach($upl_words as $key=>$word){
			if($word!='-'){
				$expressions = Dela::getLemmaVerb($word);
				if(count($expressions)){
					foreach($expressions as $expression){
						$similar_upl_words[$index_derived_upl]= $upl_words;
						$similar_upl_words[$index_derived_upl][$key] = $expression->lemma;
						$upl_str = $this->getUplFromArray($similar_upl_words[$index_derived_upl]);
						$locution = $this->getLocution($upl_str);
						if($locution)
							return $locution;
					}
				}
				$index_derived_upl++;
			}

		}

		return null;

	
	}	
	private function getLocution($upl_str){

		// if($locution = Dela::getExpression($upl_str))
		// 	return $locution;
		// else
		if($locution = Locution::getExpression($upl_str))
			return $locution;
		$tab = ['au','aux','du','des',"d'"];
		$tabr = ['à','à','de','de',"de"];
		$upl_str = str_replace($tab,$tabr,$upl_str);
		// if($locution = Dela::getExpression($upl_str))
		// 	return $locution;
		// else
		if($locution = Locution::getExpression($upl_str))
			return $locution;
		return null;
	}
		
	private function getUplFromArray($upl_words){
		$upl_str = join(' ',$upl_words);
		$upl_str = str_replace(' - ','-',$upl_str);
		$upl_str = str_replace("' ","'",$upl_str);
		return $upl_str;
	}

	private function getTheme(){
		if(session()->has('theme')){
            $theme = session()->get('theme');
        } else
            $theme = 'default';
        return $theme;
	}

	public function isOver(){
		return ($this->turn>=$this->nb_turns);
	}

}
