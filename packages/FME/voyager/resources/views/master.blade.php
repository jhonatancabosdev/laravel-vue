<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" dir="ltr">
<head>
    <title>Admin - {{ config('app.name') }} - @yield('page_title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="_token" content="{{ csrf_token() }}"/>
    <meta name="assets-path" content="{{ route('voyager.voyager_assets') }}"/>
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
    <link rel="stylesheet" href="{{ voyager_asset('css/app.css') }}">
    <script src="//code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    @yield('css')

    @if(!empty(config('voyager.additional_css')))<!-- Additional CSS -->
        @foreach(config('voyager.additional_css') as $css)<link rel="stylesheet" type="text/css" href="{{ asset($css) }}">@endforeach
    @endif

    @yield('head')
</head>

<body class="voyager @if(isset($dataType) && isset($dataType->slug)){{ $dataType->slug }}@endif">

    <div id="jq-loader" class="position-fixed h-100 w-100 flex-column flex-fill justify-content-center align-items-center" style="display: flex;">
        <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
    </div>


    <div class="app-container pb-2" id="app">
        <div class="fadetoblack d-block d-sm-none"></div>
        <div class="row content-container">

            @include('voyager::dashboard.sidebar')
            
            <div class="container-fluid">
                <div class="side-body padding-top">

                    @if($admin_logo_img = Voyager::setting('admin.icon_image', false))
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div class="logo-icon-container d-flex flex-row align-items-center">
                                        <img src="{{ Voyager::image($admin_logo_img) }}" class="img-fluid mr-3 w-100 max-w-80px">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @php
                        $segments = array_filter(explode('/', str_replace(route('voyager.dashboard'), '', Request::url())));
                        $url = route('voyager.dashboard');
                    @endphp

                    <div class="pt-0 {{ count($segments) > 0 ? 'pb-3' : '' }}">

                        <div class="col-12 w-100 d-flex flex-row flex-fill align-items-center justify-content-between">
                            <ol class="breadcrumb bg-white d-none d-md-flex {{ count($segments) > 0 ? '' : 'mb-0' }}">
                                @if(count($segments) > 0)
                                    <li class="active">
                                        <a class="text-dark" href="{{ route('voyager.dashboard')}}">{{ __('voyager::generic.dashboard') }}</a> &nbsp;
                                    </li>
                                    @foreach ($segments as $segment)
                                        @php
                                            $url .= '/'.$segment;
                                        @endphp
                                        @if ($loop->last)
                                            <li class="text-muted">/ {{ ucfirst(urldecode($segment)) }}</li> &nbsp;
                                        @else
                                            <li>
                                                <a class="text-dark" href="{{ $url }}">/ {{ ucfirst(urldecode($segment)) }}</a> &nbsp;
                                            </li>
                                        @endif
                                    @endforeach
                                @endif
                            </ol>

                            @if (count($segments) > 0)
                                <form action="{{ route('voyager.clear-cache') }}" method="POST">
                                    @csrf
                                    <button class="btn text-default text-hover-pink" data-toggle="tooltip" title="Clear all site cache.">
                                        <i class="fas fa-dumpster-fire"></i>
                                    </button>
                                </form>
                            @endif

                        </div>


                        @yield('page_header')

                    </div>

                    <div id="voyager-notifications"></div>

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="{{ voyager_asset('js/app.js') }}"></script>

    @yield('js')
    <script>
       
        @if(Session::has('alerts'))
            let alerts = {!! json_encode(Session::get('alerts')) !!};
            helpers.displayAlerts(alerts, toastr);
        @endif

        @if(Session::has('message'))

        // TODO: change Controllers to use AlertsMessages trait... then remove this
        var alertType = {!! json_encode(Session::get('alert-type', 'info')) !!};
        var alertMessage = {!! json_encode(Session::get('message')) !!};
        var alerter = toastr[alertType];

        if (alerter) {
            alerter(alertMessage);
        } else {
            toastr.error("toastr alert-type " + alertType + " is unknown");
        }
        
        @endif
    </script>

    @include('voyager::media.manager')

    @yield('javascript')

    @stack('javascript')

    @if(!empty(config('voyager.additional_js')))
        @foreach(config('voyager.additional_js') as $js)
            <script type="text/javascript" src="{{ asset($js) }}"></script>
        @endforeach
    @endif

    </body>
</html>
