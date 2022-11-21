<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="preload" href="{{ asset('/fonts/vendor/@fortawesome/fontawesome-free/webfa-regular-400.woff2?c20b5b7362d8d7bb7eddf94344ace33e') }}" as="font" type="font/woff2" crossorigin="anonymous">
        <link rel="preload" href="{{ asset('/fonts/vendor/@fortawesome/fontawesome-free/webfa-solid-900.woff2?b15db15f746f29ffa02638cb455b8ec0') }}" as="font" type="font/woff2" crossorigin="anonymous">
        <link rel="preload" href="{{ mix('css/app.css') }}" as="style">
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>@yield('title', config('app.name'))</title>
        <meta name="turbolinks-cache-control" content="no-cache">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicons/fav.png')}}">
        <meta name="msapplication-TileColor" content="#ffffff">
        <link rel=apple-touch-icon sizes=120x120 href="{{ asset('favicons/apple-touch-icon.png') }}">
        <link rel=icon type=image/png sizes=32x32 href="{{ asset('favicons/favicon-32x32.png') }}">
        <link rel=icon type=image/png sizes=16x16 href="{{ asset('favicons/favicon-16x16.png') }}">
        <link rel=manifest href="{{ asset('favicons/site.webmanifest') }}">
        <link rel=mask-icon href="{{ asset('favicons/safari-pinned-tab.svg') }}" color=#5bbad5>
        <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}">
        <meta name=msapplication-TileColor content=#006633>
        <meta name=msapplication-config content="{{asset('favicon/browserconfig.xml') }}">
        <meta name=theme-color content=#006633>
        <meta name="msapplication-TileImage" content="{{ asset('favicons/fav.png') }}">

        <meta name="_token" content="{{ csrf_token() }}" />

        <script async defer>
            var roboto = document.createElement('link');
            roboto.setAttribute("rel", "stylesheet");
            roboto.setAttribute('href', 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap');
            document.getElementsByTagName('head')[0].appendChild(roboto);
            var fjalla = document.createElement('link');
            fjalla.setAttribute("rel", "stylesheet");
            fjalla.setAttribute('href', 'https://fonts.googleapis.com/css2?family=Fjalla+One&display=swap');
            document.getElementsByTagName('head')[0].appendChild(fjalla);
        </script>

        @if(app()->environment('production'))
            <link rel="preconnect" href="//www.google-analytics.com">
            <!-- Google Tag Manager -->
            <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','{{ config('default-variables.gtm') }}');</script>
            <!-- End Google Tag Manager -->

            <script async src="//311271.tctm.co/t.js"></script>
        @endif

        <meta property="og:type" content="website"/>
        <meta property="og:url" content="{{ Request::url() }}"/>
        <meta property="og:site_name" content="{{ config('app.name') }}"/>
        @yield('meta_tags')

        @stack('stylesheet')
        @yield('css')

        @routes

    </head>
    <body class="@yield('body_class')">

        @if(config('app.env') === 'production')
            <!-- Google Tag Manager (noscript) -->
            <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ config('default-variables.gtm') }}"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
            <!-- End Google Tag Manager (noscript) -->
        @endif

        <div id="app" class="container-fluid px-0 pb-3 bg-white my-0">

            @include('front.shared.header')

            <div class="container">
                <div class="row">
                    <div class="col-12">
                        @unless (isset($isHome) || isset($isCheckout))
                            @include('front.shared.navigation.breadcrumbs')
                        @endunless
                        @unless (isset($isCheckout))
                            @if($errors->all() || session()->has('message') || session()->has('error'))
                                @include('layouts.errors-and-messages')
                            @endif
                        @endunless
                    </div>
                </div>
            </div>

            @section('content')

            @show

            @include('front.shared.footer')

        </div>

        @yield('js')

        <script src="{{ mix('js/all.min.js') }}"></script>
        <script src="{{ mix('js/app.js') }}"></script>

        @stack('scripts')

    </body>
</html>
