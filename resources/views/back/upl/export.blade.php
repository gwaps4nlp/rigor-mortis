@extends('back.template')

@section('content')

<div class="row">
  <div class="col-12">
  <h4>Export Rigor Mortis</h4>
      {!! Form::open(['url' => 'upl/export', 'method' => 'post', 'role' => 'form', 'files'=>true]) !!}
      {!! Form::control('selection', 0, 'stage_id', $errors, 'Stage',$upl_stage, null,'Select a stage...') !!}
      <div style="clear:both;"></div>
      <div class="form-group">
        <label for="type_export" class="control-label">Type of export :</label><br/>
        <input type="radio" name="type_export" value="complete" checked="checked" /> All the answers<br/>
        <input type="radio" name="type_export" value="filtered" /> Only the answers played by <input type="number" class="col-1" value="50" name="percent_players" />% of the players<br/>
        <input type="radio" name="type_export" value="only_answers" /> Only the answers<br/>
      </div>
      <div class="form-group">
        <label for="type_export" class="control-label">Options :</label><br/>
        <input type="checkbox" name="only_experts" value="1" /> Only the answers of the experts<br/>
        <input type="checkbox" name="add_usernames" value="1" /> Add the usernames in the export<br/>
      </div>
      {!! Form::submit('Export', null,['class' => 'btn btn-success']) !!}
      {!! Form::close() !!}
  </div>
</div>
@stop