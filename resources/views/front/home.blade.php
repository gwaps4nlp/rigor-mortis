@extends('front.template-upl')

@section('main')
<div class="container-fluid">
        <div id="homepage" class="row">

        </div>
        </div>


        <div class="row fixed-bottom" style="justify-content: center;">
            <div style="background-color:white; padding: 20px 5px;margin-top: 16px;margin-right:10px;">
                <a href="http://www.paris-sorbonne.fr/" target="_blank">{!! Html::image('img/logo_sorbonne_new.png','logo Sorbonne', ['style'=>'height:70px']) !!}</a>
            </div>
            <div style="background-color:white; padding: 20px 5px;">
                <a href="http://www.loria.fr/" target="_blank">{!! Html::image('img/logo_loria.png','logo Loria') !!}</a>
            </div>
            <div style="background-color:white; padding: 20px 5px;">
                <a href="http://www.inria.fr/" target="_blank">{!! Html::image('img/logo_inria.png','logo Inria') !!}</a>
            </div>
            <div style="background-color:white; padding: 20px 5px;" >
                <a href="http://www.culturecommunication.gouv.fr/Politiques-ministerielles/Langue-francaise-et-langues-de-France" target="_blank">{!! Html::image('img/logo_MCC.png','') !!}</a>
            </div>
        </div>

</div>
@stop




