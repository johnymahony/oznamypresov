<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <meta property="og:image" content="{{url('/')}}/images/oznamy-{{strtolower(city())}}-mapa.png"/>
    <meta property="og:image:width" content="1200"/>
    <meta property="og:image:height " content="630"/>
    <meta name="robots" content="index, follow">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('pageTitle', 'Úradné oznamy mesta '.city()->humanName())</title>
    @yield('metaTags')

    <link rel="shortcut icon" href="{{url('/')}}/{{city()}}-favicon.ico" type="image/x-icon">
    <link rel="icon" href="{{url('/')}}/{{city()}}-favicon.ico"
          type="image/x-icon">
    <!-- Scripts -->
    <script src="{{ url('js/jquery-3.2.1.slim.min.js') }}"></script>
    <script src="{{ mix('js/app.js') }}" defer></script>
    <script type="text/javascript" src="{{url('js/bootstrap-multiselect.js')}}"></script>
    <link rel="stylesheet" href="{{url('css/bootstrap-multiselect.css')}}" type="text/css"/>
    <script>
        var homelink = '{{url('/')}}';
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!}

            window.city = {!! json_encode(city()->toJavascript()) !!}


    </script>

    <!-- Fonts -->
    {{--<link rel="dns-prefetch" href="https://fonts.gstatic.com">--}}
    {{--<link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">--}}
    {{--    <script src="{{ url('js/popper.min.js') }}"></script>--}}
    <script defer src="{{ url('js/fa-all.js') }}"></script>

    <!-- Styles -->
    <link href="{{ mix('css/'.strtolower(city()).'.css') }}" rel="stylesheet">


    <link rel="stylesheet" href="{{ url('css/leaflet.css') }}"/>
    <script src="{{ url('js/leaflet.js') }}"></script>

    <script src="{{url('js/leaflet.markercluster/leaflet.markercluster.js')}}"></script>
    <link href="{{url('js/leaflet.markercluster/MarkerCluster.css')}}" rel="stylesheet" type="text/css">
    <link href="{{url('js/leaflet.markercluster/MarkerCluster.Default.css')}}" rel="stylesheet" type="text/css">
</head>
<body class="antialiased min-h-screen">

@yield('content')


@if(config('app.env') === 'PROD' && auth()->guest())
    @include('google_analytics')

    @if(city()->humanName() === 'Prešov')
        <!-- Global site tag (gtag.js) - Google Analytics -->

        <!-- Facebook Pixel Code -->
        <script>
            !function (f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function () {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '2215710202044036');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none" alt="fb"
                       src="https://www.facebook.com/tr?id=2215710202044036&ev=PageView&noscript=1"
            /></noscript>
        <!-- End Facebook Pixel Code -->
    @endif
    @if(city()->humanName() === 'Poprad')
        <!-- Global site tag (gtag.js) - Google Analytics -->


    @endif

@endif
@yield('footer_scripts')
</body>
</html>
