<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>{{ config('app.name') }}</title>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="{{ asset('https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js')}}"></script>
        <script src="{{ asset('https://oss.maxcdn.com/respond/1.4.2/respond.min.js')}}"></script>
        <![endif]-->
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicons/fav.png')}}">
        <meta name="turbolinks-cache-control" content="no-cache">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="{{ asset('favicons/fav.png')}}">
        <meta name="theme-color" content="#ffffff">
        <meta property="og:title" content=""/>
        <meta property="og:type" content=""/>
        <meta property="og:url" content=""/>
        <meta property="og:site_name" content=""/>
        <meta property="og:description" content=""/>
        <meta name="_token" content="{{ csrf_token() }}" />

        @yield('meta_tags')

        @stack('stylesheet')

        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        @yield('css')

    </head>
    <body>

        <div id="app">

            @section('content')
                
            @show

        </div>

        @yield('js')
        
        <script src="{{ mix('js/all.min.js') }}"></script>
        <script src="{{ mix('js/app.js') }}"></script>

        @stack('scripts')
        
    </body>
</html>
