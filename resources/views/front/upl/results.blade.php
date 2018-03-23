<?php
$data_ref = collect([]);
$data_users = collect([]);
$data = [];
?>

@extends('front.template-upl')

@section('main')
<a class="btn btn-success float-right mb-0 mt-5 mr-5" href="{{ url('game/upl') }}">{{ trans('game.back-to-menu') }}</a>
<h1>{{ trans('game.results') }}</h1>
<h2>{{  $upl_stage->description }}</h2>

<ul id="sentences_{{ $upl_stage->id }}" data-stage-id="{{ $upl_stage->id }}">
@foreach($upl_stage->reference_sentences as $sentence)
<?php 
$data[$sentence->id]=[];
$data[$sentence->id]['sentence_id']=$sentence->id;
$data[$sentence->id]['correct_upls']=$sentence->upls_ref;
$data[$sentence->id]['upls_user']=$sentence->upls_user(Auth::user()->id)->get();
?>
  <li class="card container-sentence row mb-4" data-sentence-id="{{ $sentence->id }}">
    <div class="col-12 card-body">
      <div class="sentence results-upl p-3 mb-2" id="sentence_{{ $sentence->id }}">{{ $sentence->content }}</div>
      <div id="container_upls_{{ $sentence->id }}">
      </div>
    </div>
  </li>
@endforeach
</ul>

@stop

@section('css')
<style>
.results-upl {

}
.card {
  background-color: #363636;
}
.sentence {
  background: #272822;
}
#sentence .mot, #sentence span {
  font-weight: normal;
  color: #f8f8f2;
  letter-spacing: 0.06rem;
  font-family: Arial;
  padding-left: 0.00rem;
  text-shadow: 0px 0.2rem black;
}
.upl-word {
  color: rgb(248, 248, 242);
  letter-spacing: 1px;
}
</style>
@stop

@section('scripts')
  <script>

  var upls =   {!! json_encode($data) !!};

  $( function() {
    $(".sentence").each(function(){
      var sentence = displaySentenceUpl($(this).html());
        $(this).html(sentence);
    });      
    $.each(upls, function(i,n) {
      displayAnswersUpls(n.correct_upls,n.upls_user,n.sentence_id);
    });
  });
  </script>
@stop