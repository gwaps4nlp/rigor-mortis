@extends('front.template-upl')

@section('main')
@section('main')
<div class="container">
    <div id="homepage" class="row">
<div class="card m-5">

                        
<div class="card-body text-center">
    <h1 class="text-muted">Bienvenue sur Rigor Mortis !</h1>
    <p class="card-text">Viens tester ton intuition sur les expressions multi-mots.</p>
    <p class="card-text"><a class="btn btn-success btn-lg" href="{{ route('upl-game') }}">Jouer</a></p>
     </div>
</div>

    </div>
</div>

<div class="container-fluid">
    <div class="d-flex flex-row justify-content-center">

            <div style="background-color:white; padding-top: 27px;margin-top: 16px;margin-right:10px;">
                <a href="http://www.paris-sorbonne.fr/" target="_blank">{!! Html::image('img/logo_sorbonne_new.png','logo Sorbonne', ['style'=>'height:70px']) !!}</a>
            </div>
            <div style="padding: 20px 5px;">
                <a href="http://www.loria.fr/" target="_blank">{!! Html::image('img/logo_loria.png','logo Loria') !!}</a>
            </div>
            <div style="padding: 20px 5px;">
                <a href="http://www.inria.fr/" target="_blank">{!! Html::image('img/logo_inria.png','logo Inria') !!}</a>
            </div>
            <div style="padding: 20px 5px;" >
                <a href="http://www.culturecommunication.gouv.fr/Politiques-ministerielles/Langue-francaise-et-langues-de-France" target="_blank">{!! Html::image('img/logo_MCC.png','') !!}</a>
            </div>

</div>
</div>

@stop


@section('css')
<style type="text/css">
.card {
    width:100%;
    background-color: #505050;
    border: 2px solid rgba(0, 0, 0, 0.125);
    border-radius: 14px;
    color: #F5C47D !important;
    text-shadow: -1px -1px #3F3F3F !important;
}
.text-muted {
    color: #F5C47D !important;
    text-shadow: -1px -1px #3F3F3F !important;
}
</style>
@endsection








