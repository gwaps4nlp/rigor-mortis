<?php
$data_ref = collect([]);
$data_users = collect([]);
?>

@extends('back.template')

@section('content')
<a class="btn btn-info float-right mt-3" href="{{ url('upl/export') }}">Export results</a>
<a class="btn btn-info float-right mt-3" href="{{ url('upl/admin-results/7') }}">View results</a>
<h1 class="text-center mt-3">Upl Game Gestion</h1>
<div id="accordion" role="tablist" aria-multiselectable="true">
@foreach($upl_stages as $upl_stage)
  <div class="card">
    <div class="card-header" role="tab" id="heading-{{ $upl_stage->id }}">
      <h5 class="mb-0">
        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#stage-{{ $upl_stage->id }}" aria-expanded="{{ ($open_stage==$upl_stage->id)?true:false }}" aria-controls="stage-{{ $upl_stage->id }}">
          {{  $upl_stage->label }}
        </a>
      </h5>
    </div>
    <div id="stage-{{ $upl_stage->id }}" class="collapse {{ ($open_stage==$upl_stage->id)?'show':'' }}" role="tabpanel" aria-labelledby="heading-{{ $upl_stage->id }}">
    <div class="card-body">
    <h5>{{  $upl_stage->description }}</h5>

  <div id="help_{{ $upl_stage->id }}">
  {!! $upl_stage->help !!} 
  </div>
  
  <button id="edit_help_{{ $upl_stage->id }}" class="btn btn-success edit-help" data-stage-id="{{ $upl_stage->id }}">Edit Help</button>
  <button id="show_help_{{ $upl_stage->id }}" class="btn btn-info show-help" data-stage-id="{{ $upl_stage->id }}">Show Modal</button>
	</div>
<div class="card-body">
   
  <div class="ui-widget">
    <label for="tags">Add sentence: </label>
    <input class="search" data-stage-id="{{ $upl_stage->id }}" data-stage-id="{{ $upl_stage->id }}">
  </div>
  <div class="ui-widget">
    <a class="btn btn-sm btn-success" href="{{ url('/game/upl/begin/'.$upl_stage->id.'?expert=1') }}" action="upl" id_phenomene="{{ $upl_stage->id }}">Play in expert mode</a>
    <a class="btn btn-sm btn-success" href="{{ url('/game/upl/begin/'.$upl_stage->id.'?admin=1') }}" action="upl" id_phenomene="{{ $upl_stage->id }}">Play in test mode</a>
  </div>   
  </div>
      <div class="card-body">
        
        <ul id="sentences_{{ $upl_stage->id }}" class="card-body sortable" data-stage-id="{{ $upl_stage->id }}">
        @foreach($upl_stage->reference_sentences as $sentence)
          <li class="card-body container-sentence row" data-sentence-id="{{ $sentence->id }}">
            <div class="col-8">
              <i class="fa fa-arrows handle float-left"></i>
              <span class="upl-sentence" id="sentence_{{ $sentence->id }}">{{ $sentence->content }}</span>
              <i class="fa fa-trash-o delete-sentence" aria-hidden="true" data-sentence-id="{{ $sentence->id }}"></i><br/>
              <div id="container_upls_{{ $sentence->id }}">
              <?php 
              $data_ref = $data_ref->merge($sentence->upls_ref);
              ?>
              </div>
            </div>
            <div class="col-4" id="container_upls_users_{{ $sentence->id }}">
              <?php 
              $data_users = $data_users->merge($sentence->count_upls_user);
              ?>            
            </div>
          </li>
        @endforeach
        </ul>
      </div>
    </div>
  </div>
@endforeach
</div>
@stop

@section('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>

span.validated-upl {
  margin-top: 0.25rem;
  padding: 0.25rem;
  border: 1px dashed transparent;
  border-radius: 5px;
  display: inline-block;
  position:relative;
}
div.container-validated-upl {
  display: block;
  margin-right: 1rem;
}

span.validated-upl > i {
  position: absolute;
  right:0;
  top:0;  
  visibility: hidden;
}
span.validated-upl:hover > i {
  visibility: visible;
}
span.validated-upl:hover {
  border: 1px dotted red;
  color: #4a1710; 
  visibility: visible;
}

#sentence .upl-word:hover {
    color: #00b40c !important;
}
div.word-upl {
  padding:0.5rem 1.25rem;
  margin-right:0.5rem;
  border: 1px dotted black;
  border-radius: 5px;
  background-color: #9bc5aa;
  color: #4a1710;
  display: inline-block;
  position: relative;
}
#new-upl div.word-upl:hover {
  border: 1px dotted red;
  background-color: #9bc5aa;
  color: #4a1710;
}
div.word-upl > i {
  visibility:hidden;
  position: absolute;
  right:0;
  top:0;
}
#new-upl div.word-upl:hover > i {
  visibility:visible;
}

</style>
@stop

@section('scripts')
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/ckeditor/4.5.11/ckeditor.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/ckeditor/4.5.11/adapters/jquery.js"></script>

<script>
var route_prefix = "{{ url(config('lfm.prefix')) }}";
var ckeditor_instance = 0;
$(".show-help").click(function(event){
    event.preventDefault();
    var stage_id = $(event.target).attr('data-stage-id');
    var content = $('#help_'+stage_id).html();
    newModalSimple('upl-instructions');
    $('#contentModal').html(content);
    $('#modalFooter').html("<div style=\"width:100%;\" data-dismiss=\"modal\" class=\"text-center\"><button class=\"btn btn-lg btn-success\">J'ai compris</button></div>");
    $('#modalSimple').modal('show');    
});

$(".edit-help").click(function(event){
    event.preventDefault();
    ckeditor_instance++;
  var stage_id = $(event.target).attr('data-stage-id');
  $('#edit_help_'+stage_id).hide();
  $('#show_help_'+stage_id).hide();
  var help = $('#help_'+stage_id);
  var help_text = help.html();
  var textarea = $('<textarea>'+help_text+'</textarea>');
  var button = $('<button class="btn btn-success m-1">Save</button>');
  var cancel_button = $('<button class="btn btn-warning m-1">Cancel</button>');
  var modal_button = $('<button class="btn btn-info m-1">Show modal</button>');
  $(event.target).after(textarea);
  $(textarea).after(modal_button);
  $(textarea).after(cancel_button);
  $(textarea).after(button);

    textarea.ckeditor({
      allowedContent: true,
      height: 250,
      filebrowserImageBrowseUrl: route_prefix + '?type=Images',
      filebrowserImageUploadUrl: route_prefix + '/upload?type=Images&_token={{csrf_token()}}',
      filebrowserBrowseUrl: route_prefix + '?type=Files',
      filebrowserUploadUrl: route_prefix + '/upload?type=Files&_token={{csrf_token()}}'
    });

    button.click(function(event){
      var content = textarea.val();
      $.post( base_url+'upl/edit-stage', {help:content,stage_id:stage_id,from_ck:1}, function( data ) {
        help.html(content);
        closeEditor();
      });  
      
    });
    modal_button.click(function(event){
      var content = textarea.val();
      newModalSimple('upl-instructions');
      $('#contentModal').html(content);
      $('#modalFooter').html("<div style=\"width:100%;\" data-dismiss=\"modal\" class=\"text-center\"><button class=\"btn btn-lg btn-success\">J'ai compris</button></div>");
      $('#modalSimple').modal('show');     
    });

    cancel_button.click(function(event){
      closeEditor();
    });
    function closeEditor(){
      var editor = 'CKEDITOR.instances.editor'+ckeditor_instance+'.destroy()';
      eval(editor);
      $(textarea).remove();
      $(button).remove();
      $(cancel_button).remove();
      $(modal_button).remove();
      $('#edit_help_'+stage_id).show();
      $('#show_help_'+stage_id).show();  
    }    
});

</script>
  <script>
  var upls_ref =   {!! $data_ref->toJson() !!};
  var upls_users =   {!! $data_users->toJson() !!};

  $( function() {
    $('.delete-sentence').click(deleteSentence);
    $(".upl-sentence").each(function(){
      var sentence = displaySentenceUpl($(this).html());
        $(this).html(sentence);
    });     
    $('.search').each(function(index, input){
        initAutocomplete(input);
    });
    $( ".sortable" ).sortable({
      handle: ".handle",
        revert: true,
        start: function(event, ui) {
          $(ui.item).addClass("ui-state-highlight");
        },
        stop: function(event, ui) {
          $(ui.item).removeClass("ui-state-highlight");
          saveSentencesOrder(ui.item);
      }
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
          $('#container_upls_users_'+upl.sentence_id).prepend('<div class="container-validated-upl" data-upl-index=""><span class="validated-upl">'+upl_html+' <strong>'+upl.number+'</strong></span></div>');
      });    
  });

  function saveSentencesOrder(item){

    var sentences = [];
    var sortable = $(item).closest('.sortable');
    var stage_id = $(sortable).attr('data-stage-id');
    var children = $(sortable).children('.container-sentence');
    var index=1;
        $(children).each(function() {
          var sentence_id = $(this).attr("data-sentence-id");
          if(sentence_id!=""){
            var sentence_upl_stage = {sentence_id:sentence_id, stage_id:stage_id, order:(index++)};
            sentences.push(sentence_upl_stage);
          }
        });
      if(sentences.length>0)
        $.post( base_url+'upl/update-order-sentences', {upl_stage_id:stage_id, sentences:sentences});

  }
  function deleteSentence(event){
    if(confirm('Remove sentence ?')){
      var stage_id = $(event.target).closest('.sortable').attr('data-stage-id');
      var sentence_id = $(event.target).attr('data-sentence-id');
      $.post( base_url+'upl/remove-sentence', {upl_stage_id:stage_id, sentence_id:sentence_id},function(data){
        $(event.target).closest('.container-sentence').remove();
      });
    }
  }
  function initAutocomplete(input){
    $(input).autocomplete({
      minLength: 2,
      source: base_url + "/sentence/search",
      select: function( event, ui ) {
        var stage_id = $(event.target).attr('data-stage-id');
      	var sentence_id = ui.item.id;
      	this.value = '';
    		$.post( base_url+'upl/add-sentence', {stage_id : stage_id, sentence_id: sentence_id}, function( data ) {
          var new_sentence = $('<li class="card-body container-sentence" data-sentence-id="'+sentence_id+'">').append('<i class="fa fa-arrows handle float-left"></i>'+ui.item.content+'<i class="fa fa-trash-o delete-sentence" aria-hidden="true" data-sentence-id="'+sentence_id+'"></i>');
    			$('#sentences_'+stage_id).prepend(new_sentence);
          saveSentencesOrder(new_sentence);
          $('.delete-sentence[data-sentence-id='+sentence_id+']').click(deleteSentence);
    		});
 
        return false;
      }
    })

    .autocomplete( "instance" )._renderItem = function( ul, item ) {
      return $( "<li class='ui-menu-item'>" )
        .append( $("<div class='ui-menu-item-wrapper'>").append(item.content) )
        .appendTo( ul );
    };
  }

  </script>
@stop