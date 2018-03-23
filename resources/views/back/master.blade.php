<?php
use Gwaps4nlp\Core\Models\ConstantGame;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="author" content="Loria et U. Paris-Sorbonne" />
		<meta name="description" content="@yield('description')" />
		<meta name="csrf-token" content="{{ csrf_token() }}">   
        <title>@yield('title') - Rigor Mortis</title>
        <link rel="shortcut icon" type="image/x-icon" href="{!! asset('img/favicon.ico') !!}" />

        <!-- CSS principal -->
        {!! Html::style(mix("build/css/app.css")) !!}
		
		@yield('css')
			
    </head>
    <body>
        @include('front.navbar')
        <div class="container-admin container-fluid">
          @yield('container')
		  @yield('main')
        </div>
        <script>
            @include('js.data-js')
        </script>
        <script src="{{ asset(mix("build/js/all.js")) }}"></script>
		
		@yield('scripts')
        <script>
          function resizeIframe(obj) {
            obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
            var arrFrames = parent.parent.document.getElementsByTagName("IFRAME");
            for (var i = 0; i < arrFrames.length; i++) {
              if (arrFrames[i].name != obj.name) {
                resizeIframe(arrFrames[i]);
              }
            }        
          }
          function resizeIframeAgain(obj) {
            obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
            var arrFrames = parent.document.getElementsByTagName("IFRAME");
            for (var i = 0; i < arrFrames.length; i++) {

              if (arrFrames[i].name != obj.name) {
                resizeIframeAgain(arrFrames[i]);
              }
            }        
          }
        </script>
    </body>
</html>
