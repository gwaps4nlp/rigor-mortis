@extends('front.template')

@section('main')

<div id="home">

		{!! Html::image('img/labo-opacity-reduce.jpg','Points',['style'=>'z-index:-1;position:absolute;top:0;left:0;width:100%;border-radius:1% 1%;']) !!}
		{!! Html::image('img/tuyaux-small.png','Tuyau',['style'=>'z-index:1;position:absolute;margin-top:41.4%;left:0%;width:12.9%;']) !!}
		{!! Html::image('img/porte-document.png','Points',['style'=>'z-index:1;position:absolute;margin-top:44%;left:18%;width:81%;']) !!}
		<div class="label" style="position: absolute;margin-top: 42.2%;left: 22%;">
			News
		</div>
		<div class="label" style="position: absolute;margin-top: 45.2%;left: 42%;">
			Mon compte
		</div>
		<div class="label" style="position: absolute;margin-top: 41.7%;left: 58%;">
			{{ trans('site.statistics') }}
		</div>
		<div id="news" onclick="$('#modalNews').modal();">
			@if(count($news))
	            {!! substr(strip_tags($news->first()->content),0,70).'...' !!}<br/>
	            <span class="link curvilinear">lire la suite...</span>
            @endif
		</div>

		<div id="zombie-level" style="position:absolute;margin-top:20%;left:7%;width:19.5%;">
			{!! Html::image('img/momie-1.png','RigorMortis',array('style'=>'z-index:0;margin:0;width:100%;')) !!}
		</div>

		<div class="modal fade" id="modalNews" role="dialog">
		    <div class="modal-dialog">
			    <div class="modal-content">
			        <div class="modal-body">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
						<h2>News</h2>
						<div  style="text-align:left;">
						@foreach($news as $new)
							{{ substr($new->created_at,0,10) }} : {!! $new->content !!}<br/>
						@endforeach
						</div>
			            <div class="modal-footer">
	  						<button type="button" class="btn btn-danger" data-dismiss="modal">{{ trans('site.close') }}</button>
						</div>			                					       	
			        </div>
			    </div>
		    </div>
		</div>
		<div id="account">
            <span class="link" id="changePassword">Modifier mon mot de passe.</span><br />
            <span class="link" id="changeEmail">Envoi des emails.</span><br />
            <span class="link" onclick="$('#modalDeleteAccount').modal();">Supprimer mon compte.</span><br />
		</div>
		<div class="modal fade" id="modalChangePassword" role="dialog">
		    <div class="modal-dialog">
		    {!! Form::open(['url' => 'password/change', 'method' => 'post', 'role' => 'form', 'id'=>'form-change-password']) !!} 
			    <div class="modal-content">
			        <div class="modal-body">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
						<h2>Modification du mot de passe</h2>
						   <span id="error-change-password" class="error"></span>
				          
				            <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
				              <label for="password">{{ trans('site.new-password') }}</label>
				              <input type="password" class="form-control" name="password" id="password" placeholder="{{ trans('site.placeholder-enter-password') }}">
				              {{ $errors->first('password', '<small class="help-block">:message</small>') }}
				            </div>
				            <div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
				              <label for="password_confirmation">{{ trans('site.confirm-password') }}</label>
				              <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="{{ trans('site.placeholder-confirm-password') }}">
				              {{ $errors->first('password_confirmation', '<small class="help-block">:message</small>') }}
				            </div>
			          	                					       	
			            <div class="modal-footer">
				            <button type="submit" class="btn btn-success">
				            	{{ trans('site.button-validate') }}
				            </button>
				            <button type="submit" class="btn btn-danger btn-default" data-dismiss="modal">
				            	{{ trans('site.cancel') }}
				            </button>
						</div>			        
			        </div>        
			    </div>
			{!! Form::close() !!}
		    </div>
		</div>

		<div class="modal fade" id="modalChangeEmail" role="dialog">
		    <div class="modal-dialog">
		    {!! Form::open(['url' => 'user/change-email', 'method' => 'post', 'role' => 'form', 'id'=>'form-change-email']) !!} 
			    <div class="modal-content">
			        <div class="modal-body">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
						<h2>Envoi des emails</h2>
						   <span id="error-change-email" class="error"></span>
				          
				            <div class="form-group {{ $errors->has('email_frequency_id') ? 'has-error' : '' }}">
				              <label for="frequency">{{ trans('site.email-frequency') }}</label>
				              <div style="text-align:left;">
					              @foreach($email_frequency as $frequency)
					              	@if($frequency->id == $user->email_frequency_id)
					              		<input type="radio" name="email_frequency_id" value="{{ $frequency->id }}" checked="checked"/> {{ trans('site.'.$frequency->slug) }}<br/>
					              	@else
										<input type="radio" name="email_frequency_id" value="{{ $frequency->id }}" /> {{ trans('site.'.$frequency->slug) }}<br/>
					              	@endif
					              @endforeach
				              </div>
				              {!! $errors->first('email_frequency_id', '<small class="help-block">:message</small>') !!}
				            </div>
				            <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
				              <label for="email">{{ trans('site.email') }}</label>
				              <input type="text" class="form-control" name="email" id="email" value="{{ $user->email }}">
				              {{ $errors->first('email', '<small class="help-block">:message</small>') }}
				            </div>
			          	                					       	
			            <div class="modal-footer">
				            <button type="submit" class="btn btn-success">{{ trans('site.button-validate') }}</button>
				            <button type="submit" class="btn btn-danger btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
						</div>			        
			        </div>        
			    </div>
			{!! Form::close() !!}
		    </div>
		</div>
		<div class="modal fade" id="modalDeleteAccount" role="dialog">
		    <div class="modal-dialog">
			    <div class="modal-content">
			        <div class="modal-body">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
						<h2>Suppression du compte</h2>
				          {!! Form::open(['url' => 'user/delete', 'method' => 'get', 'role' => 'form', 'id'=>'form-delete']) !!} 
				            <div class="form-group">
				            	{{ trans('site.confirm-delete-account') }}
				            </div>
				            <button type="submit" class="btn btn-success">{{ trans('site.button-validate') }}</button>
				            <button type="submit" class="btn btn-danger btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
				          {!! Form::close() !!}		                					       	
			            <div class="modal-footer">

						</div>			        
			        </div>        
			    </div>
		    </div>
		</div>

		@if(session()->has('message'))
			<div class="alert" role="alert" style="position: absolute;">
				{{ session('message') }}
			</div>
		@endif
 	
</div>

@stop
@section('scripts')
<script>
    var index=0;

    function init() {

        @if(isset($_GET['email']))	
        	$('#modalChangeEmail').modal('show');
        @elseif(isset($_GET['password']))	
        	$('#modalChangePassword').modal('show');
        @endif
    }

    window.onload = init();
</script>
@stop

@section('css')
<style>

#header_new {
	z-index:1;
	position:relative;
}
@media (min-width: 1200px) {
	#home.col-md-10{
		width:85.333333%;
		left:-1%;
	}
}
#home, #home .link{
	border-radius : 1% 1%;
    position:relative;
	color:#3c1715;
}
#scores{
	position: absolute;
	margin-top: 18%;
	margin-left: 15%;
	font-family: "Charlemagne Std Bold"; 
}
.col-lg-10{
	padding-left:0;
	padding-right:0;
}
.herbe {
	display:none;
}
img#trophies{
	width: 2.5vw;
	position: relative;
	left: 0vw;
}
img#money{
	width: 6vw;
	position: relative;
	left: -1.7vw;
}
img#points{
	width: 4vw;
}
img#level{
	width: 4vw;
}
img.level {
    width: 70%;
    position: absolute;
    margin-top: 107%;
    left: -39%;
}
#leader-board {
	width: 101%;
	padding-top: 10%;
	font-family: "Charlemagne Std Bold";
	position: absolute;
	margin-top: -17%;
	margin-left: 18%;
	color:white;	
}
#number-friends {
    width: 70%;
    font-family: "Charlemagne Std Bold";
    position: absolute;
    margin-top: 64%;
    margin-left: 0%;
    color: #FFF;
    z-index: 2;
    text-align: center;
    vertical-align: middle;
    padding: 6% 1%;
    cursor:pointer;
}
#friends {
	font-family: "Charlemagne Std Bold";
	position: absolute;
	margin-top: 87%;
	margin-left: -36%;
	color: #FFF;
	z-index: 2;
	text-align: center;
	vertical-align: middle;
	cursor: pointer;
	width: 41%;
	line-height:0.9vw;
	overflow:hidden;
}
img.level-friend {
    width: 66%;
    padding-bottom:10%;
}
#label-friend {
	width: 80%;
	color: #000;
	font-size: 0.7vw;
	left: 0px;
	position: absolute;
	top: 85%;
}
#leader-board img{
    width:100%;
    position:absolute;
    left:0;
}
#periode-board{
	position: absolute;
	margin-top: 39%;
	padding-left:59%;
	color: #FFF;
	font-size: 0.9vw;
	font-family: "anothershabby";
	right:30%;
	text-align: right;
	width:100%;
	z-index:2;
}
#periode-board .focus{
	font-size: 1.4vw;
	text-align: left;
	position: absolute;
	left: 48%;
	top: 34%;
}
#toggleScore{
	text-align: left;
	position: absolute;
	left:48%;
	cursor:pointer;
}
.periode-choice{
	text-transform: uppercase;
	cursor:pointer;
}
#my-position{
	text-transform: uppercase;
	cursor:pointer;
	font-size: 0.5vw;	
}
.periode-choice:hover{
	text-shadow:0px 0px 5px #a8a8a8;	
}
.rank_neighbor{
	display:none;
}
.rank.self, .rank_neighbor.self{
	color:#fffd6c;
}
#leaders-1-2{
    font-size:0.8vw;
    position:absolute;    
    margin-top: 73%;
    text-align: left;
    padding-left:26%;
}
#leaders-3-4-5{
    font-size:0.8vw;
    margin-top: 127%;
    position:absolute;
    text-align: left;
    padding-left:14%;      
}
#panel-trophies{
	background-image: url("../img/white-background.png");
	position: absolute;
	width: 320px;
	border-radius: 12px;
	left: 79.5%;
	margin-top: -76%;
	font-family: "Charlemagne Std Bold";
	font-weight: bold;
	font-size: 1.1vw;
	text-transform: uppercase;
	z-index:1;
}
#panel-trophies td.img{
	width:29%;
}
#panel-trophies td.text{
	vertical-align:middle;
	padding-left:4%;
}
#panel-duel{
	background-image: url("../img/white-background.png");
	position: absolute;
	width: 18%;
	border-radius: 12px;
	left: 20.5%;
	margin-top: 21%;
	font-family: "Charlemagne Std Bold";
	font-weight: bold;
	font-size: 1.1vw;
	text-transform: uppercase;
}
#panel-duel td.img{
	width:29%;
}
#panel-duel td.text{
	vertical-align:middle;
	padding-left:4%;
}
#block-trophies {
	cursor:pointer;
}
#block-trophies > #panel-trophies {
	display:none;
}
#block-trophies:hover > #panel-trophies {
	display:block;
}
div.label {
	font-family: "Charlemagne Std Bold";
	font-weight: bold;
	font-size: 1.7vw;
	text-transform: uppercase;
	color:#261012;
}
div.label-friends {
	font-family: "Charlemagne Std Bold";
	font-weight: bold;
	font-size: 1.1vw;
	text-transform: uppercase;
	color:#261012;
}
#stats {
	position: absolute;
	margin-top: 51.3%;
	left: 61.5%;
	z-index: 2;
	font-family: "anothershabby";
	font-weight: normal;
	width: 15%;
	line-height: 1.5em;
	font-size: 1.2vw;
}
#news {
    position: absolute;
	margin-top: 51.2%;
	left: 21%;    
    z-index: 2;
    font-family: "";
    font-weight: normal;
    font-size: 1.3vw;
    width: 18%;
    font-style: italic;
    line-height:1.2em;
}
#account {
	position: absolute;
	margin-top: 54%;
	left: 45.5%;
	z-index: 2;
	font-weight: normal;
	width: 12%;
	font-family: "anothershabby";
	font-size: 1.2vw;
	line-height: 1.2em;
}
#panel-trophies span.label{
	font-family: "anothershabby";
	text-transform: none;
	color:#261012;
	font-weight:normal;
	font-size:1vw;
}
#panel-duel span.label{
	font-family: "anothershabby";
	text-transform: none;
	color:#261012;
	font-weight:normal;
	font-size:1vw;
}
#stats span{
	font-family: "Charlemagne Std Bold";
	font-weight: normal;
	font-size:1.3vw;
}
.curvilinear {
	font-family: "anothershabby";
}
label {
	color : white;
}
</style>
@stop

