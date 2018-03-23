<?php
$data_ref = collect([]);
$data_users = collect([]);
?>

@extends('front.template')

@section('main')

<div class="row">

  <div class="col-6  my-4">
    <h1>Résultats</h1>
    <h2>{{  $upl_stage->description }}</h2>
  </div>
  <div class="col-6 mt-4">
    <a class="btn btn-success float-right" href="{{ url('upl/admin-index') }}">Retour au menu</a>
    {!! Form::open(['url' => $params['path'], 'method' => 'get', 'role' => 'form', 'class' => 'form-inline', 'style' => 'width:50%;','id'=>"admin-results"]) !!}     
    <label>Phrases par page : </label>
    {!! Form::control('selection', 0, 'sentences-per-page', $errors, '',$perPage,null,null,$params['sentences-per-page']) !!}   
    {!! Form::close() !!} 

    {{ $sentences->links() }}
  </div>
</div>
<ul id="sentences_{{ $upl_stage->id }}" class="card-body sortable" data-stage-id="{{ $upl_stage->id }}">
@foreach($sentences as $sentence)
  <?php 
  $upls_ref = $sentence->upls_ref;
  ?>
  <li class="card-body container-sentence row" data-sentence-id="{{ $sentence->id }}">
  @if($upls_ref->count()>0)
    <div class="col-8">
  @else
    <div class="col-12">
  @endif
      <span class="results-upl sentence upl-sentence p-1" id="sentence_{{ $sentence->id }}">{{ $sentence->content }}</span>
      <div id="container_upls_users_{{ $sentence->id }}">
        <?php 
        $distinct_players = $sentence->count_distinct_players();
        ?>
        @if($distinct_players>1)
          <span id="distinct_players_{{ $sentence->id }}">{{ $distinct_players }}</span> joueurs ont donné leur avis :
        @elseif($distinct_players==1)
          <span id="distinct_players_{{ $sentence->id }}">{{ $distinct_players }}</span> joueur a donné son avis :
        @else
          Aucun joueur n'a donné son avis.
        @endif
        <?php 
        $data_users = $data_users->merge($sentence->count_upls_user()->orderBy('words_positions','desc')->get());
        ?>            
      </div>
    </div>
    @if($upls_ref->count()>0)
      <div class="col-4" id="container_upls_{{ $sentence->id }}">
        <?php 
        $data_ref = $data_ref->merge($sentence->upls_ref);
        ?>
      </div>  
    @endif  
  </li>
@endforeach
</ul>
<div class="text-center">
{{ $sentences->links() }}
</div>
@stop

@section('css')
<style>
.results-upl {

}
</style>
@stop

@section('scripts')
  <script>

  var upls_ref =   {!! $data_ref->toJson() !!};
  var upls_users =   {!! $data_users->toJson() !!};

  $( function() {

    $(document).on('change', "#sentences-per-page" ,function( event ) {
        event.preventDefault();
        $('#admin-results').submit();
    });    
    $(".upl-sentence").each(function(){
      var sentence = displaySentenceUpl($(this).html());
        $(this).html(sentence);
    });     
      $(upls_ref).each(function(index_upl,upl){

          var upl_html = '';
          if(upl.words_positions=="0"){
              upl_html+='<div class="word-upl" data-word-position="0">Aucune upl dans cette phrase</div>';
          } else {
              var words_positions = upl.words_positions.split('-');
              $(words_positions).each(function(index,word_position){
                  var word = $('#sentence_'+upl.sentence_id+' > #word_index_'+word_position).html();
                  upl_html+='<div class="word-upl" data-word-position="'+word_position+'">'+word+'</div>';
              });
          }
          $('#container_upls_'+upl.sentence_id).prepend('<div class="container-validated-upl" data-upl-index=""><span class="validated-upl">'+upl_html+'</span></div>');
      });
      $(upls_users).each(function(index_upl,upl){
          var distinct_players = parseInt($('#distinct_players_'+upl.sentence_id).html(),0);
          var upl_html = '';
          if(upl.words_positions=="0"){
              upl_html+='<div class="word-upl" data-word-position="0">Aucune upl dans cette phrase</div>';
          } else {
              var words_positions = upl.words_positions.split('-');
              $(words_positions).each(function(index,word_position){
                  var word = $('#sentence_'+upl.sentence_id+' > #word_index_'+word_position).html();
                  upl_html+='<div class="word-upl" data-word-position="'+word_position+'">'+word+'</div>';
              });
          }
          $('#container_upls_users_'+upl.sentence_id).append('<div class="container-validated-upl" data-upl-index=""><span class="validated-upl">'+upl_html+' <strong>'+upl.number+'</strong></span> ('+Math.round(upl.number/distinct_players*100,2)+'%)</div>');
      });    
  });


  </script>
@stop