<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="preload" href="{{ mix('css/app.css') }}" as="style">
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <meta charset="utf-8">
        <title>{{ config('app.name') }} - Admin</title>
        <meta name="Description" content="Tools and Supplies for Public Works and Plumbing Professionals.">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="{{ asset('https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js')}}"></script>
        <script src="{{ asset('https://oss.maxcdn.com/respond/1.4.2/respond.min.js')}}"></script>
        <![endif]-->
        <link rel="icon" href="{{ asset('favicons/32.png') }}" sizes="32x32">
        <link rel="icon" href="{{ asset('favicons/57.png') }}" sizes="57x57">
        <link rel="icon" href="{{ asset('favicons/76.png') }}" sizes="76x76">
        <link rel="icon" href="{{ asset('favicons/96.png') }}" sizes="96x96">
        <link rel="icon" href="{{ asset('favicons/128.png') }}" sizes="128x128">
        <link rel="icon" href="{{ asset('favicons/228.png') }}" sizes="228x228">
        <link rel="shortcut icon" href="{{ asset('favicons/128.png') }}" sizes="128x128">
        <link rel="apple-touch-icon" href="{{ asset('favicons/96.png') }}" sizes="96x96">
        <link rel="apple-touch-icon" href="{{ asset('favicons/128.png') }}" sizes="128x128">
        <link rel="apple-touch-icon" href="{{ asset('favicons/228.png') }}" sizes="228x228">
        <meta name="msapplication-TileColor" content="#fff">
        <meta name="msapplication-TileImage" content="{{ asset('favicons/228.png') }}">
        <meta name="msapplication-config" content="{{ asset('favicons/browserconfig.xml') }}" />
        <meta property="og:title" content="{{ config('app.name') }} - Admin"/>
        <meta property="og:type" content=""/>
        <meta property="og:url" content="{{ request()->url() }}"/>
        <meta property="og:site_name" content="{{ config('app.name') }}"/>
        <meta property="og:description" content="{{ config('app.description') }}"/>
        <meta name="_token" content="{{ csrf_token() }}" />
        @yield('javascript')
        @yield('og')
        @stack('stylesheet')
        @yield('css')
    </head>
    <body class="bg-white">
        <div id="app">
            <div class="container bg-white" style="min-height: 500px;">
                <div class="row">
                    <div class="col-12 text-center">
                        @include('layouts.errors-and-messages')
                    </div>
                </div>
                <div class="jq-page-content">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 d-flex align-items-center justify-content-center" style="min-height: 500px;">
                                <div class="d-flex">
                                    <form class="p-4 bg-white shadow rounded-lg" action="{{ route('voyager.login') }}" method="POST" style="min-width: 400px;">
                                        <div class="mb-3">
                                            <a class="px-0 py-3 text-center d-block" href="{{ route('home') }}">
                                                <img
                                                    src="{{ asset('images/logo.png') }}"
                                                    class="img-fluid mr-0 mr-sm-3 position-relative"
                                                    style="max-width: 250px;"
                                                    alt="{{ config('app.name') }}"
                                                >
                                            </a>
                                        </div>
                                        {{ csrf_field() }}
                                        <div class="form-group form-group-default" id="emailGroup">
                                            <label>{{ __('voyager::generic.email') }}</label>
                                            <div class="controls">
                                                <input type="text" name="email" id="email" value="{{ old('email') }}" placeholder="{{ __('voyager::generic.email') }}" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="form-group form-group-default" id="passwordGroup">
                                            <label>{{ __('voyager::generic.password') }}</label>
                                            <div class="controls">
                                                <input type="password" name="password" placeholder="{{ __('voyager::generic.password') }}" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="form-group" id="rememberMeGroup">
                                            <div class="controls">
                                                <input type="checkbox" name="remember" id="remember" value="1">&nbsp;<label for="remember" class="remember-me-text">{{ __('voyager::generic.remember_me') }}</label>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-block btn-danger shadow-sm rounded-lg">Login</button>
                                        
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            var btn = document.querySelector('button[type="submit"]');
            var form = document.forms[0];
            var email = document.querySelector('[name="email"]');
            var password = document.querySelector('[name="password"]');
            btn.addEventListener('click', function(ev){
                if (form.checkValidity()) {
                    btn.querySelector('.signingin').className = 'signingin';
                    btn.querySelector('.signin').className = 'signin hidden';
                } else {
                    ev.preventDefault();
                }
            });
            email.focus();
            document.getElementById('emailGroup').classList.add("focused");

            // Focus events for email and password fields
            email.addEventListener('focusin', function(e){
                document.getElementById('emailGroup').classList.add("focused");
            });
            email.addEventListener('focusout', function(e){
               document.getElementById('emailGroup').classList.remove("focused");
            });

            password.addEventListener('focusin', function(e){
                document.getElementById('passwordGroup').classList.add("focused");
            });
            password.addEventListener('focusout', function(e){
               document.getElementById('passwordGroup').classList.remove("focused");
            });
        </script>
        <script src="{{ mix('js/app.js') }}"></script>
    </body>
</html>