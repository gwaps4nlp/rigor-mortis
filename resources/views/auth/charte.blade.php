@extends('front.template-upl')

@section('main')

	@if($errors->has('message'))
	    @include('partials/error', ['type' => 'danger', 'message' => $errors->first('message')])
	@endif

	@if($errors->has('email_reset'))
	    @include('partials/error', ['type' => 'danger', 'message' => $errors->first('email_reset')])
	@endif

	@if (session('status'))
		@include('partials/error', ['type' => 'success', 'message' => session('status')])
	@endif


	<div class="pb-5">
	<div class="container">
		<div class="row">
		    <div class="col-12 p-5 card" id="charte">
			@include('lang/'.App::getLocale().'/charte')
		    </div>
		</div>
	</div>
	</div>

@stop

@section('css')
<style type="text/css">
.card {
	background-color : #505050;
	border: 2px solid rgba(0, 0, 0, 0.125);
	border-radius: 14px;
	color: #F5C47D !important;
}

</style>
@endsection