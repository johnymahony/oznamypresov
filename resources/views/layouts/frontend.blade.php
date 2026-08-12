@extends('layouts.html')

@section('content')
    <header class="lg:px-16 px-6 bg-white  lg:py-0 py-2 z-50 fixed w-full"
            id="navbar">
        <div class="w-1500 flex flex-wrap justify-between items-center mx-auto">
            <div class="flex justify-between items-center">
                <a href="{{ url('/') }}">
                    <img src="{{url('/')}}/images/{{config('app.city')}}-logo.png" align="center" class="logo"
                         alt="{{city()->domain()}}">
                </a>
            </div>
            <div>
                @if(config('app.env') === 'PROD')
                    <a href="https://www.patreon.com/bePatron?u=17679019"
                       data-patreon-widget-type="become-patron-button">Stante sa Patrónom</a>
                    <script async src="https://c6.patreon.com/becomePatronButton.bundle.js"></script>
                @endif
            </div>
            <label for="menu-toggle" class="pointer-cursor lg:hidden block">
                <svg class="fill-current text-gray-900" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                     viewBox="0 0 20 20"><title>menu</title>
                    <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"></path>
                </svg>
            </label>
            <input class="hidden" type="checkbox" id="menu-toggle"/>

            <div class="hidden lg:flex lg:items-center lg:w-auto w-full" id="menu">
                <nav>
                    <ul class="lg:flex items-center justify-between text-base text-gray-700 pt-4 lg:pt-0">
                        @guest
                            <li><a class="nav-link lg:p-4"
                                   href="{{ url('/') }}">{{ __('Všetky oznamy') }}</a></li>
                            {{--                    <li><a class="nav-link" href="{{ url('projekty') }}">{{ __('Projekty') }}</a></li>--}}
                            {{--                    <li><a class="nav-link" href="{{ url('mapa/'.date('Y')) }}">{{ __('Mapa') }}</a></li>--}}
                            {{--                    <li><a class="nav-link" href="{{ url('objednavky') }}">{{ __('Objednávky') }}</a></li>--}}
                            <li><a class="nav-link lg:p-4"
                                   href="{{ url('ako-to-funguje') }}">{{ __('Ako to funguje?') }}</a></li>
                            <li><a class="nav-link lg:p-4"
                                   href="{{ url('contact') }}">{{ __('Kontakt') }}</a></li>
                            <li><a class="nav-link lg:p-4"
                                   href="{{ url('grafy') }}">{{ __('Štatistiky') }}</a></li>
                        @else
                            <li><a class="nav-link lg:p-4"
                                   href="{{ url('/') }}">{{ __('Zoznam') }}</a></li>
                            @if(auth()->user()->isAdmin())
                                <li><a class="nav-link lg:p-4"
                                       href="{{ url('/cityzens') }}">{{ __('Občania') }}</a></li>
                                <li><a class="nav-link lg:p-4"
                                       href="{{ url('/work') }}">{{ __('Mestá') }}</a></li>
                                <li><a class="nav-link lg:p-4"
                                       href="{{ url('/problems') }}">{{ __('Problémy') }}</a></li>
                                <li><a class="nav-link lg:p-4"
                                       href="{{ url('/reports/emails') }}">{{ __('Report email') }}</a></li>
                                <li><a class="nav-link lg:p-4"
                                       href="{{ url('/reports/boards_sent') }}">{{ __('Boards Sent') }}</a></li>
                            @endif
                            <li>
                                <a class="nav-link lg:p-4" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        @endguest

                    </ul>
                </nav>

            </div>
        </div>
    </header>
    <main>
        <div class="mx-auto pt-57">
            @yield('content_frontend')
        </div>
        <div class="p-4 text-center mx-auto">
            <h2 class="mb-4">Darujte dobrovoľný príspevok (platba kartou)</h2>
            <div class="text-center mx-auto w-48">
                @if(config('app.env') === 'PROD')
                    <a href="https://www.patreon.com/bePatron?u=17679019"
                       data-patreon-widget-type="become-patron-button">Stante
                        sa Patrónom</a>
                    <script async src="https://c6.patreon.com/becomePatronButton.bundle.js"></script>
                @endif
            </div>
            @if(config('app.env') === 'PROD')
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                    <input type="hidden" name="cmd" value="_s-xclick"/>
                    <input type="hidden" name="hosted_button_id" value="E8D25KHJEKD9N"/>
                    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0"
                           name="submit" title="PayPal - Jednoduchý a bezpečný spôsob online platby!"
                           alt="Darujte cez PayPal"/>
                    <img border="0" src="https://www.paypal.com/en_SK/i/scr/pixel.gif" width="1" height="1"
                         alt=""/>
                </form>
            @endif
            <br>
            <br>
            <br>
            <br>
            <br>
        </div>
    </main>
    <footer class="footer">
        Oznamy {{city()->humanName()}} &copy; <span onclick="window.location.href='/login'">{{date('Y')}}</span>
        <a class="underline text-blue-500 hover:underline"
                href="/sitemap.xml">Mapa
            stránok</a>
        @if(city()->egovPage())
            <a href="{{city()->egovPage()}}" class="underline text-blue-500 hover:underline">
                Egov {{city()->humanName()}}</a>
        @endif
    </footer>
@endsection