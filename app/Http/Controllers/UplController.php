<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\UplStage;
use App\Models\SentenceUpl;
use App\Models\Upl;
use Gwaps4nlp\Core\Models\Role;
use Gwaps4nlp\Core\Models\Source;
use App\Models\SentenceUplStage;
use App\Models\SentenceUplUser;
use App\Models\Sentence;
use App\Repositories\UplStageRepository;
use App\Repositories\SentenceUplStageRepository;
use App\Exceptions\UplGameException;
use Response, DB;

class UplController extends Controller
{

    /**
     * Create a new UplController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin',['except'=>['getIndex','getResults']]);

    }
    
    /**
     * Display the index
     *
     * @return Illuminate\Http\Response
     */
    public function getIndex(UplStageRepository $upl_stages, SentenceUplStageRepository $sentence_upl_stages, Request $request)
    {

        $user = auth()->user();

        $training_done = true;
        
        $demo_stage = $upl_stages->getStagesByUser($user, 'demo')->first();

        $training_stages = $upl_stages->getStagesByUser($user, 'training');
        
        foreach($training_stages as $stage){
            if(!$stage->done){
                $training_done = false;
            }
        }
        
        $game_stages = $upl_stages->getStagesByUser($user, 'game');
        
        $scores = $upl_stages->getScores();

        /* Choice of the graphic theme */
        if($request->has('theme')){
            $request->session()->put('theme', $request->input('theme'));
            $theme = $request->input('theme');
        }
        elseif($request->session()->has('theme')){
            $theme = $request->session()->get('theme');
        } else
            $theme = 'default';

        $game_stage = UplStage::find(7);
        $count_sentences_not_done = $sentence_upl_stages->countNotDone($game_stage, $user);
        $count_sentences = $sentence_upl_stages->count($game_stage);

        return view('front.upl.index',compact('demo_stage','training_stages','game_stages','game_stage','training_done','scores','theme','count_sentences_not_done','count_sentences'));
    }

    /**
     * Display the index
     *
     * @return Illuminate\Http\Response
     */
    public function getResults(UplStageRepository $upl_stages, $id)
    {
        $user = auth()->user();
        
        $upl_stage = $upl_stages->getStageUserById($user, $id);
        if(!$upl_stage->done)
            throw new UplGameException("Tu dois d'abord terminer le niveau pour accéder à tes résultats.");
        return view('front.upl.results',compact('upl_stage'));
    }

    /**
     * Display the index
     *
     * @return Illuminate\Http\Response
     */
    public function getAdminResults(Request $request, UplStageRepository $upl_stages, $id)
    {
        $perPage = array(20=>20,50=>50,100=>100);
        $params['sentences-per-page']= (in_array($request->input('sentences-per-page'),$perPage))? $request->input('sentences-per-page') : 20;
        $params['path'] = 'upl/admin-results/'.$id;
        $upl_stage = $upl_stages->getById($id);
        $sentences = $upl_stage->sentences()->paginate($params['sentences-per-page']);
        $sentences->appends(array('sentences-per-page'=>$params['sentences-per-page']));
        return view('back.upl.results',compact('upl_stage','sentences','perPage','params'));
    }

    /**
     * Send an export of the result of the game
     *
     * @return Illuminate\Http\Response
     */
    public function getExport(UplStageRepository $upl_stages)
    {
        $upl_stage = $upl_stages->getList();
        return view('back.upl.export',compact('upl_stage'));
    }

    /**
     * Send an export of the result of the game
     *
     * @return Illuminate\Http\Response
     */
    public function postExport(Request $request, UplStageRepository $upl_stages)
    {

        $request->validate([
            'stage_id' => 'required|exists:upl_stages,id',
            'type_export' => 'required',
        ]);

        $upl_stage = $upl_stages->getById($request->input('stage_id'));

        $add_usernames = $request->input('add_usernames');
        $only_experts = $request->input('only_experts');
        
        $role_expert = Role::where('slug','=','expert')->first();

        if($only_experts)
            $experts = $role_expert->users()->get()->pluck('id');
        else
            $experts = [];

        $type_export = $request->input('type_export');
        if($type_export=='complete')
            $percent_min_players = 0;
        elseif($type_export=='only_answers')
            $percent_min_players = 0;
        else
            $percent_min_players = $request->input('percent_players');

        $sentences = $upl_stage->sentences()->orderBy('stage_order','desc')->get();
        $pathToFile = storage_path('export/'.date('YmdHis'));
        $file = fopen($pathToFile,"w");

        foreach($sentences as $sentence){
            $export = "";
            $lines = [];
            $tokens = Upl::splitSentence($sentence->content);

            $distinct_players = $sentence->count_distinct_players();
            // $distinct_players = $sentence->count_distinct_players(null, [796,1158,1180]);
            foreach($tokens as $key=>$token){
                $lines[$key+1]=[];
                $lines[$key+1]['token']=$token;
                $lines[$key+1]['nsp']='_';
                $lines[$key+1]['upls']=[];
            }
            

            

            if($type_export=='only_answers'){
                $export.= "# sentid\t".$sentence->sentid."\n";
                $export.= "# players\t".$distinct_players."\n";
                $export.= "# sentence :\t".$sentence->content."\n";
                
                // $upls_users = $sentence->count_upls_user([796,1158,1180])->orderBy('number','desc')->with('upl')->get();
                $upls_users = $sentence->count_upls_user()->orderBy('number','desc')->with('upl')->get();

                $correct_upls = SentenceUpl::select('upl_id')->where('sentence_id',$sentence->id)->where('source_id',Source::getReference()->id)->with('upl')->get();
                foreach($correct_upls as $correct_upl){
                    $export.="# solution\t".$correct_upl->upl->content."\n";
                }

                foreach($upls_users as $upl){
                    $words_position = explode('-',$upl->words_positions);
                    $number_answers = $upl->number;
                    $percent_players = round(100*$number_answers/$distinct_players,2);
                    
                    if($percent_players < $percent_min_players)
                        continue;

                    if(count($words_position)>1){
                        $export.= $upl->upl->content;
                    } else {
                        $export.= "no mwe";
                    }
                    $export.= "\t".$number_answers."\t".$percent_players."%";
                    $export.= "\n";
                }                
            } else {
                
                if($sentence->isReference())
                    $export.= "## Phrase de référence\n";

                $upls_users = $sentence->count_upls_user([], $experts)->orderBy('words_positions','asc')->with('upl')->get();

                $index_upls = 1;
                foreach($upls_users as $upl){
                
                    $words_position = explode('-',$upl->words_positions);
                    $number_answers = $upl->number;
                    $percent_players = round(100*$number_answers/$distinct_players,2);
                    
                    if($percent_players < $percent_min_players)
                        continue;

                    if(count($words_position)>1){
                        $export.= "# ".$index_upls." : ";
                        foreach($words_position as $position){
                            $export.= $lines[$position]['token']." ";
                            $lines[$position]['upls'][]=$index_upls;
                        }
                        $index_upls++;
                    } else {
                        $export.= "# no mwe ";
                    }
                    if(!$only_experts){
                        $export.= "- ".$number_answers." players (".$percent_players."%)";
                        $export.= "\n";
                    }
                    if($add_usernames){
                        if($only_experts)
                            $upls_user = SentenceUplUser::where('sentence_upl_id',$upl->sentence_upl_id)->whereIn('user_id',$experts)->with('user')->get();
                        else
                            $upls_user = SentenceUplUser::where('sentence_upl_id',$upl->sentence_upl_id)->with('user')->get();
                        $usernames = [];
                        foreach($upls_user as $upl_user){
                            $usernames[] = $upl_user->user->username;   
                        }
                        $export.= "## ".implode(' - ',$usernames);
                        $export.= "\n";
                    }
                }

                foreach($lines as $index=>$line){
                    $export.= $index."\t".$line['token']."\t".$line['nsp']."\t".join(';',$line['upls'])."\n";
                }

            }
            $export.= "\n";
            fputs($file, $export);
        }
// $upl_stage = $upl_stages->getList();
//         return view('back.upl.export',compact('upl_stage'));
        return response()->download($pathToFile, 'export.csv');
    }

    /**
     * Send an export of the result of the game
     *
     * @return Illuminate\Http\Response
     */
    public function postExportByUser(Request $request, UplStageRepository $upl_stages)
    {

        $request->validate([
            'stage_id' => 'required|exists:upl_stages,id',
            'type_export' => 'required',
        ]);

        $upl_stage = $upl_stages->getById($request->input('stage_id'));

        $type_export = $request->input('type_export');
        if($type_export=='complete')
            $percent_min_players = 0;
        elseif($type_export=='only_answers')
            $percent_min_players = 0;
        else
            $percent_min_players = $request->input('percent_players');

        $sentences = $upl_stage->sentences()->orderBy('stage_order','desc')->get();
        $pathToFile = storage_path('export/'.date('YmdHis'));
        $file = fopen($pathToFile,"w");

        foreach($sentences as $sentence){
            $export = "";
            $lines = [];
            $tokens = Upl::splitSentence($sentence->content);

            $distinct_players = $sentence->count_distinct_players([1158,1180]);
            foreach($tokens as $key=>$token){
                $lines[$key+1]=[];
                $lines[$key+1]['token']=$token;
                $lines[$key+1]['nsp']='_';
                $lines[$key+1]['upls']=[];
            }
            

            if($type_export=='only_answers'){
                $upls_users = $sentence->count_upls_user()->whereIn('user_id',[1158,1180])->orderBy('number','desc')->with('upl')->get();



                $correct_upls = SentenceUpl::select('upl_id')->where('sentence_id',$sentence->id)->where('source_id',Source::getReference()->id)->with('upl')->get();

                if(count($upls_users)<1) continue;

                $export.= "# sentid\t".$sentence->sentid."\n";
                $export.= "# players\t".$distinct_players."\n";
                $export.= "# sentence :\t".$sentence->content."\n";          
                foreach($correct_upls as $correct_upl){
                    $export.="# solution\t".$correct_upl->upl->content."\n";
                }

                foreach($upls_users as $upl){
                    $words_position = explode('-',$upl->words_positions);
                    $number_answers = $upl->number;
                    $percent_players = round(100*$number_answers/$distinct_players,2);
                    
                    if($percent_players < $percent_min_players)
                        continue;

                    if(count($words_position)>1){
                        $export.= $upl->upl->content;
                    } else {
                        $export.= "no mwe";
                    }
                    $export.= "\t".$number_answers."\t".$percent_players."%";
                    
                    $players = SentenceUplUser::select('user_id')
                        ->where('sentence_upl_id',$upl->sentence_upl_id)
                        ->whereIn('user_id',[1158,1180])
                        ->groupBy('user_id')
                        ->get();
                    $export.= "\t";
                    $export.= $players->implode('user_id', ', ');

                    $export.= "\n";
                }                
            } else {
                
                $upls_users = $sentence->count_upls_user()->orderBy('words_positions','asc')->with('upl')->get();

                $index_upls = 1;
                foreach($upls_users as $upl){
                
                    $words_position = explode('-',$upl->words_positions);
                    $number_answers = $upl->number;
                    $percent_players = round(100*$number_answers/$distinct_players,2);
                    
                    if($percent_players < $percent_min_players)
                        continue;

                    if(count($words_position)>1){
                        $export.= "# ".$index_upls." : ";
                        foreach($words_position as $position){
                            $export.= $lines[$position]['token']." ";
                            $lines[$position]['upls'][]=$index_upls;
                        }
                        $index_upls++;
                    } else {
                        $export.= "# no mwe ";
                    }
                    $export.= "- ".$number_answers." players (".$percent_players."%)";
                    $export.= "\n";
                }

                foreach($lines as $index=>$line){
                    $export.= $index."\t".$line['token']."\t".$line['nsp']."\t".join(';',$line['upls'])."\n";
                }

            }
            $export.= "\n";
            fputs($file, $export);
        }
        return 'haha';
        // return response()->download($pathToFile, 'export.csv');
    }

    /**
     * Display the index of back office
     *
     * @return Illuminate\Http\Response
     */
    public function getAdminIndex(Request $request)
    {
        $upl_stages = UplStage::all();
        if($request->has('open'))
            $open_stage = $request->input('open');
        else
            $open_stage = 0;
        return view('back.upl.index',compact('upl_stages','open_stage'));
    }

    /**
     * Remove a sentence to a stage
     *
     * @return Illuminate\Http\Response
     */
    public function postAddSentence(Request $request)
    {
        $upl_stage = UplStage::findOrFail($request->input('stage_id'));
        $sentence = Sentence::findOrFail($request->input('sentence_id'));
        $sentence_upl = SentenceUplStage::firstOrCreate(['upl_stage_id'=>$upl_stage->id, 'sentence_id'=>$sentence->id, 'stage_order'=>0]);
        return $sentence_upl->id;
    }

    /**
     * Save the modifications of a stage
     *
     * @return Illuminate\Http\Response
     */
    public function postEditStage(Request $request)
    {
        $upl_stage = UplStage::findOrFail($request->input('stage_id'))->update($request->all());
        return '';
    }
    /**
     * Remove a sentence from a stage
     *
     * @return Illuminate\Http\Response
     */
    public function postRemoveSentence(Request $request)
    {
        $upl_stage = UplStage::findOrFail($request->input('upl_stage_id'));
        $sentence = Sentence::findOrFail($request->input('sentence_id'));
        SentenceUplStage::where(['upl_stage_id'=>$upl_stage->id, 'sentence_id'=>$sentence->id])->delete();
        SentenceUpl::where(['sentence_id'=>$sentence->id, 'source_id'=>Source::getReference()->id])->delete();
        return '';
    }

    /**
     * Update the order of the sentences of a stage
     *
     * @param  App\Http\Requests\CorpusCreateRequest $request     
     * @return Illuminate\Http\Response
     */
    public function postUpdateOrderSentences(Request $request)
    {
        $upl_stage_id = $request['upl_stage_id'];
        $upl_stage = UplStage::findOrFail($upl_stage_id);
        foreach($request['sentences'] as $sentence){
            $sentence_upl_stage = SentenceUplStage::where('sentence_id', $sentence['sentence_id'])->where('upl_stage_id', $upl_stage->id)->firstOrFail();
            $sentence_upl_stage->stage_order = $sentence['order'];
            $sentence_upl_stage->save();
        }
        return Response::json(['id'=>0]);
    } 

}
