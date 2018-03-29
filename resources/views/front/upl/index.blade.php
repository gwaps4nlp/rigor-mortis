@extends('front.template-upl')

@section('main')
<div class="px-0" id="index-upl-game">
    <div id="block-game">
		@if (session()->has('error'))
			@include('partials/error', ['type' => 'danger', 'message' => session('error')])
		@endif    

		<div class="row">
		    <div class="pt-3 col-12 col-lg-9">
				
	 				@if($demo_stage->done)
					<div class="card disabled-card">
						<div class="next-level-done text-muted">Phase débloquée
							<div>
								<a class="btn btn-success" href="{{ url('upl/results/'.$demo_stage->id) }}">
									Voir mes résultats
								</a>
							</div>
						</div>
						
						<div class="card-body" style="opacity:0.15;">
					@else
					<div class="card active-card">
						<div class="card-body">
					@endif 

					<!-- <div class="card-body"> -->
				    <h4 class="card-title">1<sup>re</sup> phase</h4>
				    <h6 class="card-subtitle mb-2 text-muted">Test de l’intuition des explorateurs.</h6>
				    <p class="card-text">Viens tester ton intuition sur les expressions multi-mots.</p>
				    @if(!$demo_stage->done)
				    	<button class="btn btn-success btn-lg link-level" action="upl" id_phenomene="{{ $demo_stage->id }}">Jouer</button>
				    @endif
				  </div>
				</div>

					
					@if(!$demo_stage->done)
					<div class="card active-card">
						<div class="next-level text-muted">Accès restreint : débloque d'abord la 1<sup>re</sup> phase !</div>
						<div class="card-body" style="opacity:0.15;">
					@elseif($training_done)
					<div class="card disabled-card">
						<div class="next-level-done text-muted">Phase débloquée
							<div>
						    @foreach($training_stages as $upl_stage)
							    <button class="btn btn-success link-level mt-1" href="#" action="upl" id_phenomene="{{ $upl_stage->id }}">{{$upl_stage->label}}</button>
						    @endforeach
							</div>
						</div>
						
						<div class="card-body" style="opacity:0.15;">	
					@else
					<div class="card active-card">
						<div class="card-body">
					@endif
					    <h4 class="card-title">2<sup>e</sup> phase</h4>
					    <h6 class="card-subtitle mb-2 text-muted">Formation des explorateurs.</h6>
					    <p class="card-text">Viens te former à la découverte des expressions multi-mots dans les couloirs de la pyramide.</p>
					    @if(!$training_done)
					    @foreach($training_stages as $upl_stage)
						    @if($upl_stage->done)
						    <button class="btn btn-success btn-lgé link-level mt-1" href="#" action="upl" id_phenomene="{{ $upl_stage->id }}">{{$upl_stage->label}}</button>
						    @else
						    <button class="btn btn-upl-training link-level mt-1" href="#" action="upl" id_phenomene="{{ $upl_stage->id }}">{{$upl_stage->label}}</button>
						    @endif
					    @endforeach
					    @endif
					  </div>
					</div>
					
					@if(!$training_done)
					<div class="card disabled-card">
						<div class="next-level text-muted">Accès restreint : débloque d'abord la 2<sup>e</sup> phase !</div>
						<div class="card-body" style="opacity:0.15;">
					@elseif(!$count_sentences_not_done)
					<div class="card disabled-card">
						<div class="next-level-done text-muted">Niveau terminé !
							<div>
								<a class="btn btn-success" href="{{ url('upl/results/'.$game_stage->id) }}">
									Voir mes résultats
								</a>
							</div>
						</div>
						<div class="card-body" style="opacity:0.15;">
					@else
					<div class="card active-card">
						<div class="card-body">
					@endif		
					    <h4 class="card-title">3<sup>e</sup> phase</h4>
					    <h6 class="card-subtitle mb-2 text-muted">Exploration libre.</h6>
					    <p class="card-text">Lance-toi à la chasse aux expressions multi-mots !</p>
					    @if($training_done && $count_sentences_not_done)
					    	<button class="btn btn-success btn-lg link-level" href="#" action="upl" id_phenomene="7">Jouer</button>
					    @endif
					  </div>
					</div>
					<div class="card active-card">
					@if($count_sentences_not_done)
						<div class="next-level text-muted">Accès restreint : débloque d'abord la 3<sup>e</sup> phase !</div>
						<div class="card-body" style="opacity:0.15;">
					@else
						<div class="card-body">
					@endif
					    <h4 class="card-title">Niveau bonus</h4>
					    <h6 class="card-subtitle mb-2 text-muted">Exploration libre.</h6>
					    <p class="card-text">Continue l'aventure avec de nouvelles phrases !</p>
					    <button class="btn btn-success btn-lg link-level" href="#" action="upl" id_phenomene="8">Jouer</button>
					  </div>
					</div>

		    </div>
		    <div class="col-12 col-lg-3 pt-lg-3 pl-lg-0">
			    <div class="card disabled-card">
				    <div class="card-body">
				    	<h4 class="text-muted">Classement</h4>
				    	<table class="table table-stripped leaderboard-upl">
				    	<?php
				    	$rank = 0;
				    	$number_users = 0;
				    	$current_score=0;
				    	?>
				    	@foreach($scores as $score)
				    		<?php 
				    			$number_users++;
				    			if($current_score!=$score->score)
				    				$rank = $number_users;

			    				$current_score=$score->score;
				    		?>
				    		<tr class="{{(Auth::user()->id==$score->user_id)?'self':''}}">
				    			<td>{{ $rank }} - {{ $score->username }}</td>
					    		@if(Auth::user()->isAdmin())
				    				<td>{{ $score->email }}</td>
					    		@endif
				    			<td>{{ $score->score }}</td>
				    		</tr>
				    	@endforeach
				    	</table>
				    </div>
			    </div>
			    <div class="card disabled-card">
				    <div class="card-body">
				    	<h4 class="text-muted">Choix du thème</h4>
				    	<a class="btn btn-success mt-1" href="{{ route('upl-game') }}?theme=pyramids-dark">Pyramides dark</a>
				    	<a class="btn btn-success mt-1" href="{{ route('upl-game') }}?theme=pyramids-sable">Pyramides sable</a>
				    </div>
			    </div>
		    </div>
	    </div>
    </div>
</div>

@if(!$demo_stage->done)
	@include('partials.upl.modal-welcome')
@endif

@stop

@section('scripts')
<script type="text/javascript">
@if(!$demo_stage->done)
	$(document).ready(function(){
		$('#modalWelcomeUpl').modal('show');
	});
@endif
</script>
@stop

@section('css')
<style type="text/css">

/*#body-game {
	background-image: url("../img/pyramides.png");
    background-color: rgb(235, 188, 120);
    height: 90vh;
}*/
@if($theme=="pyramids-dark"||$theme=="default")
.text-muted {
	color: rgba(199,161,116,1) !important;
	color: #F5C47D !important;
	text-shadow: -1px -1px #3F3F3F !important;
}
.card-subtitle {
	color: #F5C47D !important;
	text-shadow: 0px 0px #3F3F3F !important;
}
#container-game {
	background-color: #505050;
	background-color: #363636;

	color: #F5C47D !important;
}
.card {
	background-color : #505050;
	border: 2px solid rgba(0, 0, 0, 0.125);
	border-radius: 14px;
}
#sentence {
	background: #272822;
}
#sentence .mot, #sentence span {
    font-weight: normal;
	color: #f8f8f2;
	letter-spacing: 0.06rem;
	/*font-family: Arial;*/
	padding-left: 0.00rem;
	/*text-shadow: 0px 0.2rem black;*/
}
#block-game {
    opacity: 0.98;
}
#index-upl-game {
	opacity: 1;
}
#new-upl {
    background-color: #272822;
}

div.word-upl {
    border: 1px dotted black;
	background-color: #272822;
	color: #F5C47D !important;
    
}
#container-upl div.word-upl {
    border: 1px dotted black;
	background-color: #505050;
}
#sentence .upl-word:hover {
    color: #bdc6fc !important;
}

@elseif($theme=="pyramids-sable")
.card {
	position: relative;
	width: 100%;
	margin-bottom:1rem;
	text-align: center;
	background-color : #D4AC71;
	border: 2px solid rgba(0, 0, 0, 0.125);
	border-radius: 14px;
}
.card-body {
	color: rgba(250,250,250,1) !important;
	text-shadow: -1px -1px #3F3F3F !important;	
}
@else
.card {
	position: relative;
	width: 100%;
	margin-bottom:1rem;
	text-align: center;
	background-color : #9BC5AA;
	border: 1px solid rgba(0, 0, 0, 0.125);
	border-radius: 0.25rem;
}
#body-upl {
	background-image: url("../img/background-home.png"); 
}
.container-upl {
	background-color: #0e7f3c;
	color: #fff;
}
.card-body {
	color: rgba(250,250,250,1) !important;
}
.container-game #block-game {
    opacity: 1;
}
@endif
.card {
	position: relative;
	width: 100%;
	margin-bottom:1rem;
	text-align: center;
}
tr.self {
	color : #fffd6c;
}

#block-game {
    opacity: 0.97;
}

table.leaderboard-upl td {
 text-align : left;
 padding: 0.1rem;
}
table.leaderboard-upl td + td{
 text-align : right;
}

.btn-upl-training:hover {
    color: #fff;
    background-color: #D9BE72;
    border-color: #D9BE72;
}
.btn-upl-training {
	color: #fff;
	background-color: #D2AA6D;
	border-color: #D9BE72;
}

.disabled-card {
	/*min-height: 100px;*/
}
.active-card {
	min-height: 200px;
}
.card-body {
	width: 100%;
	height: 100%;
	top: 0;
	left: 0;
}
.next-level {
	z-index: 10;
	text-align: center;
	font-size: 2rem;
	padding-top: 40px;
	width: 100%;
	height: 100%;
	top: 0;
	left: 0;
	position : absolute;	
}
.next-level-done {
	z-index: 10;
	text-align: center;
	font-size: 2rem;
	padding-top: 10px;
	width: 100%;
	height: 100%;
	top: 0;
	left: 0;
	position : absolute;
}
</style>

@stop



