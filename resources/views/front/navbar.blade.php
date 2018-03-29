<div id="navbar-top" class="bg-light fixed-top pt-0 pb-0">
	<nav class="container navbar navbar-expand-sm navbar-light">
		<a class="navbar-brand"  href="{!! url('') !!}">
		{!! Html::image('img/logo.png','logo',array('id'=>'rigormortis-logo','style'=>'height:34px;')) !!} 
		</a>
		<div id="">
			<ul class="navbar-nav">
				<li class="d-none d-md-block nav-item">
					<a class="nav-link rounded-btn {{ Request::is('/')?'active':'' }}" href="{!! asset('') !!}">Accueil</a>
				</li>
				<li class="d-none d-md-block nav-item dropdown {{ Request::is('game')?'active':'' }}">
					<a class="nav-link rounded-btn" href="{!! url('game/upl') !!}" style="margin-bottom:5px;">
						{{ trans('site.play') }}
					</a>
				</li>
				@if(Auth::check() && Auth::user()->isAdmin())
					<li class="d-none d-xl-block nav-item {{ Request::is('admin')?'active':'' }}">
						<a class="nav-link rounded-btn" href="{!! url('admin') !!}">{{ trans('site.admin') }}</a>
					</li>	
				@endif
				<li class="d-none d-md-block nav-item {{ Request::is('faq')?'active':'' }}">
					<a class="nav-link rounded-btn" href="{!! url('faq') !!}">{{ trans('site.faq') }}</a>
				</li>		
			</ul>
		</div>
	<div class="topbar-right align-self-end ml-auto" style="font-size: 15px;">
		@if(Auth::check())
		<div class="topbar-username dropdown ">
			<div class="dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown" data-dropdown-hover-all="true" aria-haspopup="true" aria-expanded="false">
				{{-- <a class="rounded-avatar" href="{!! url('home') !!}">
					<img src="{{ asset('img/level/thumbs/'.Auth::user()->level->image) }}" alt="{{ Auth::user()->username }}" />
				</a>--}}
				<span class="username">{{ Auth::user()->username }}</span>
				<i class="fa fa-chevron-down pl-1 pr-2"></i>
			</div>
			<ul class="dropdown-menu" aria-labelledby="dropdownMenu1" style="top:95%">
				<li><a href="{!! url('user/home') !!}">Mon laboratoire</a></li>
				@if(Auth::user()->isAdmin())
					<li class="d-xl-none"><a href="{!! url('admin') !!}">Administration</a></li>
				@endif
				<li><a href="{!! url('user/home?email') !!}">Réception des emails</a></li>
				<li><a href="{!! url('user/home?password') !!}">Mot de passe</a></li>
				<li class="d-lg-none"><a href="{!! url('user/players') !!}">Classement</a></li>
				<li role="separator" class="divider"></li>
				<li><a href="{!! route('logout') !!}">Fermer la session</a></li>
			</ul>
		</div>
		@endif
		<div id="information" class="d-none d-md-block" style="float:left;width:31px;height:41px;padding-top:3px;margin-right:20px;margin-left:10px;" data-offset="0 0" data-toggle="tooltip" data-placement="bottom" title="{{ trans('site.informations') }}">
			<a href="{!! route('informations') !!}" >
				<img src="{{ asset('img/infos.png') }}"  style="float:left;width:31px;height:41px;" />
			</a>
		</div>

		<div style="float:left;width:31px;height:41px;padding-top:3px;margin-right:20px;margin-left:10px;" class="d-md-none">
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target=".collapse" aria-controls=".collapse" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
		</div>

	</div>

	</nav>

	<div class="collapse">
		<ul class="navbar-nav">
			<li class="nav-item">
				<a class="nav-link rounded-btn {{ Request::is('/')?'active':'' }}" href="{!! asset('') !!}">Accueil</a>
			</li>
			<li class="nav-item">
				<a class="nav-link rounded-btn" href="{!! url('game/upl') !!}" style="margin-bottom:5px;">
					{{ trans('site.play') }}
				</a>
			</li>
			@if(Auth::check() && Auth::user()->isAdmin())
				<li class="nav-item {{ Request::is('admin')?'active':'' }}">
					<a class="nav-link rounded-btn" href="{!! url('admin') !!}">{{ trans('site.admin') }}</a>
				</li>	
			@endif
			<li class="nav-item {{ Request::is('faq')?'active':'' }}">
				<a class="nav-link rounded-btn" href="{!! url('faq') !!}">{{ trans('site.faq') }}</a>
			</li>
			<li class="nav-item {{ Request::is('infos')?'active':'' }}">
				<a class="nav-link rounded-btn" href="{!! route('informations') !!}">{{ trans('site.informations') }}</a>
			</li>
		</ul>
	</div>

</div>