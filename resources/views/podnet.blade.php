@extends('layouts.frontend')

@section('pageTitle', $board->description)
@section('metaTags')
    <meta property="og:url" content="{{$board->url()}}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="{{$board->description}}"/>
    <meta property="og:description" content="{{$board->title}}"/>
@endsection
@section('content_frontend')
    @prod
    <div id="fb-root"></div>
    <script>(function (d, s, id) {
            window.fbAsyncInit = function () {
                FB.init({
                    appId: '2129549447268014',
                    autoLogAppEvents: true,
                    xfbml: true,
                    version: 'v3.1',
                });
            };

            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s);
            js.id = id;
            js.src = 'https://connect.facebook.net/sk_SK/sdk.js#xfbml=1&version=v3.0';
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
        @if(request()->get('share', false))
        $(document).ready(function () {
            setTimeout(function () {
                FB.ui({
                    method: 'share',
                    href: '{{$board->url()}}',
                }, function (response) {
                    console.log('FB response', response);
                });
            }, 300);
        });
        @endif
    </script>
    @endprod
    <div class="page">
        <h1 class="h1">{{$board->title}}</h1>
        <p class="my-6">{{$board->description}}</p>
        <h4>{{skDate($board->published_at)}} / {{$board->category->name}}</h4>
        <div class="my-4">
            <span class="font-bold">Riešiteľ:</span> {{$board->person}}<br>
        </div>
        <div class="my-4">
            <span class="font-bold">Riešenie:</span> {{$board->solution}}<br>
        </div>
        <div class="my-4">
            <span class="font-bold">Dátum
            riešenia: </span>{{($board->solution_date && !preg_match("/0000/", $board->solution_date))? skDate($board->solution_date) : '-'}}
        </div>
        <div class="my-4">
            <span class="font-bold">Stav riešenia: </span>{{$board->solution_state}}<br>
        </div>
        <p>
            @prod
        <div class="fb-share-button"
             data-href="{{$board->url()}}"
             data-layout="button_count">
        </div>
        <a href="fb-messenger://share/?link={{urlencode($board->url())}}&app_id=2129549447268014">Poslať cez FB
            Messenger</a>
        @endprod
        </p>
        @include('partial.map', ['boards' => [$board]])
        <hr>
        <div id="mapid" class="" @if(isset($notInMap->list[$board->id])) style="display: none" @endif></div>
    </div>
@endsection
