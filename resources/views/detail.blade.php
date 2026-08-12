@extends('layouts.frontend')

@section('pageTitle', $board->description)
@section('metaTags')
    <meta property="og:url" content="{{$board->url()}}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="{{$board->description}}"/>
    <meta property="og:description" content="{{$board->title}}"/>
    <meta name="description" content="{{$board->title}}"/>
    <meta name="keywords" content="{{$board->category->name}}"/>
@endsection
@section('content_frontend')
    @guest
        @include('subscribe_popup')
        <div class="text-white p-2 form-bg">
            @include('partial.subscribe-form')
        </div>
    @endguest
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
                shareFb('{{$board->url()}}');
            }, 300);
        });
        @endif
    </script>
    @endprod
    <div class="my-6 p-6 page">
        <a href="{{$board->url()}}" class="hover-link">
            <h2 class="text-lg lg:text-2xl leading-snug">{{$board->description}}</h2>
        </a>
        <p class="text-sm mt-2 mb-6"><span class="text-gray-500">{{skDate($board->published_at)}}</span> / @include('partial.category-link')</p>
        @include('partial.board.download-button')
        @include('components.button', [
        'label' => 'Zdieľať na Facebook',
        'classes' => 'my-1 inline-block',
        'onclick' => 'shareFb(\''.$board->url().'\')'
        ])
        <a href="fb-messenger://share/?link={{urlencode($board->url())}}&app_id=2129549447268014" target="_blank">
            @include('components.button', [
            'label' => 'Poslať cez FB Messenger',
            'classes' => 'my-1 inline-block',
            ])
        </a>

        @if(($countAttachments = $board->attachments->count()) > 0)
            <iframe src="{{url('/download/'.$board->id.'/inline')}}" class="pdf-iframe my-6" frameborder="0"></iframe>
        @endif
        @include('partial.map', ['boards' => [$board]])
        <hr>
        <div id="mapid" class=""></div>
    </div>
@endsection
