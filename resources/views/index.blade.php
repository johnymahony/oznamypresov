@extends('layouts.frontend')
@section('metaTags')
    <meta property="og:url" content="{{url('/')}}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="Úradné oznamy mesta {{city()->humanName()}}"/>
    <meta property="og:description" content="Zapíšte sa na odber úradných oznamov"/>
    <meta name="description" content="Zapíšte sa na odber úradných oznamov"/>
    <meta name="keywords" content="oznamy {{strtolower(city())}}, {{strtolower(city())}}, oznamy, uradna tabula, podnety, stavebny urad"/>
@endsection
@section('content_frontend')
    @guest
        @include('subscribe_popup')
        <div class="text-white p-2 form-bg">
            @include('partial.subscribe-form')
        </div>
    @endguest
    <div class="mx-auto my-6 px-4 pt-0 w-1500">
        <div class="flex flex-col md:flex-row">
            <div class="w-full lg:w-1/4">
                @include('components.panel', [
                'title' => 'Filter',
                'content' => view('partial.boards-filter', [
                    'categories' => $categories,
                    'year' => $year,
                    'search' => $search,
                    'filter' => $filter,
                ])
            ])
            </div>
            <div class="w-full lg:w-3/4 lg:pl-6">
                @if($boards->count() === 0)
                    <h1 class="h1">Žiadne oznamy nenájdené</h1>
                    <p class="py-4">Skúste zmeniť nastavenia filtra, alebo <a href="/" class="link">filter zrušiť</a>
                    </p>
                @else
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
                    </script>
                    @endprod
                    <a name="mapa"></a>
                    @include('partial.map', ['allBoards' => $allBoards])
                    <div id="mapid" class=""></div>
                    <span class="text-gray-500 pt-4 inline-block">Nie všetky oznamy sú zaznačené na mape. <a
                                href="{{url('/ako-to-funguje')}}#mapa" class="underline">Prečo?</a></span>
                    <div class="pt-4">


                        <div class="mt-8 mb-4">
                            {{ $boards->links() }}
                        </div>
                        @foreach($boards as $board)
                            @include('partial.board-list-item', [
                                'board' => $board
                            ])
                        @endforeach
                        {{ $boards->links() }}
                        @endif
                    </div>
            </div>
        </div>
    </div>
    @if($filter)
        <script>
            $(document).ready(() => {
                scrollTo(0, $('#mapid').offset().top)
            });
        </script>
    @endif

@endsection
