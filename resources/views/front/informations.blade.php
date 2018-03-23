@extends('front.template')

@section('main')
	<div id="informations" class="p-md-5">
		@include('lang/'.App::getLocale().'/informations')
	</div>
@stop

@section('scripts')
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
});
</script>
@stop

@section('css')
<style>
.license img{
	width:60px;	
}
</style>
@stop