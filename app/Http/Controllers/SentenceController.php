<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Sentence;
use App\Http\Controllers\Controller;
use Response, DB;

class SentenceController extends Controller
{
    
    /**
     * Create a new SentenceController instance.
     *
     * @param  App\Repositories\SentenceRepository $sentences
     * @return void
     */
    public function __construct()
    {
        $this->middleware('ajax')->only('getSearch');
    }

    /**
     * Search sentences
     *
     * @return Illuminate\Http\Response
     */
    public function getSearch(Request $request)
    {   
        $search = $request->input('term');
        $sentences = Sentence::select('id','content')->where('content','like','%'.$search.'%')->limit(10)->get();
        $data =[];
        foreach($sentences as $sentence){
            $data[] = array(
                'id' => $sentence->id,
                'label' => $sentence->content,
                'value' => $sentence->content,
            );
        }
        return Response::json($sentences);
    }

}
