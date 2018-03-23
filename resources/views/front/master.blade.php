<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="author" content="Loria et U. Paris-Sorbonne" />
        <meta name="description" content="@yield('description')" />
        <meta name="csrf-token" content="{{ csrf_token() }}">        
        <title>@yield('title') - Rigor Mortis</title>
        <link rel="shortcut icon" type="image/x-icon" href="{!! asset('img/favicon.ico') !!}" />

        {!! Html::style(mix("build/css/app.css")) !!}
        
        @yield('css')

    </head>
    <body class="{{ App::environment('local')?'test':'' }}">

        @include('front.navbar')
        
        @yield('container')

        <div id="containerModal"></div>
        <script>
            @include('js.data-js')
        </script>

        <script src="{{ asset(mix("build/js/all.js")) }}"></script>

        @yield('scripts')

        <input type="hidden" id="connected" value="{{ (Auth::check())?Auth::user()->id:'0' }}" />
        <?php
        $new_log = Session::has('inputs.new_log');
        $number_upls_not_seen = 0;
        if(Auth::check()){
            $upls_users = App::make('App\Repositories\SentenceUplUserRepository');
            $upls_user_not_seen = $upls_users->getNotSeen(Auth::user());
            $number_upls_not_seen = $upls_user_not_seen->count();
        }
        if($number_upls_not_seen || $new_log){
            $modal = App::make('App\Services\Html\ModalBuilder');
            $news = App::make('Gwaps4nlp\NewsManager\Repositories\NewsRepository');
            $count_news_not_seen = $news->countNotSeen(Auth::user());
            $news_not_seen = $news->getNotSeen(Auth::user());

            if($number_upls_not_seen || $news_not_seen){
                $html = '';
                if($new_log)
                    $html .= '<h2>Salut '.Auth::user()->username.' !</h2>';
                if($number_upls_not_seen){
                    $upls_users->resetNotSeen(Auth::user());
                    $html .= '<h5>D\'autres joueurs ont joué comme toi :</h5>';
                    $html .= '<h4>Tu as gagné '.$upls_user_not_seen->sum('points_not_seen').' points à Rigor Mortis !</h4>';
                    if($new_log) {
                        $html .= '<div class="mb-3" style="text-align:center;">';
                        $html .= '<a class="btn btn-success" href="'.url('game/upl').'">Jouer à Rigor Mortis</a>';
                        $html .= '</div>';
                    }
                }
                if($new_log) {
                    if($count_news_not_seen){
                        $html .= '<h3>Il y a du nouveau sur Rigor Mortis</h3>';
                        $html .= '<div style="text-align:left;">';
                        foreach($news_not_seen as $new_not_seen){
                            $html .= $new_not_seen->content.'<hr/>';
                            $new_not_seen->users()->updateExistingPivot(Auth::user()->id,['seen'=>1]);
                        }
                        $html .= '</div>';
                    }
                    $html.='<div style="text-align:center;">';

                }
                $html.='</div>';
                echo $modal->modal($html,'modalLogin');

                echo "<script>
                        $('#modalLogin').modal('show');
                    </script>";
            }
        }
        ?>


    </body>
</html>
