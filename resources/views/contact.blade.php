@extends('layouts.frontend')

@section('content_frontend')
    <div class="page policy-page p-6">

        <h1>Oznamy mesta {{city()->humanName()}}</h1>
        <p>Tento informačný portál vznikol v hlavách Prešovčanov - poslanca Richarda Drutarovského a informatika Ivana
            Ivanca. S
            programovaním pomohli František T. a Cyril D.</p>
        <p>Bol naprogramovaný bez nároku na odmenu vo voľnom čase, a spustený do prevádzky zadarmo.</p>
        <p>Poďakovanie patrí aj mestu {{city()->humanName()}}, ktoré sprístupnilo svoje dáta v strojovo spracovateľnej
            forme.</p>
        @if(city()->egovPage())
            Viac rôzných dát z daného mesta nájdete na <a href="{{city()->egovPage()}}" class="underline text-blue-500 hover:underline">
                 {{city()->egovPage()}}</a>
        @endif
        <p>Portál funguje aj v iných mestách:</p>
        <p>
            @foreach(cities() as $cityClass)
                @php $city = resolve($cityClass) @endphp
                {{$city->humanName()}}: <a href="https://{{$city->domain()}}" target="_blank"
                                           class="hover:underline text-blue-500">www.{{$city->domain()}}</a><br>

            @endforeach
        </p>
        <p>Ak sa Vám tento portál páči, možete nás pozvat na pivo :)</p>
        <p>Ak sa Vám tento portál nepáči, veľmi pekne prosím napíšte nám, čo sa Vám nezdá.</p>
        <p>V prípade akýchkoľvek otázok nás neváhajte kontaktovať emailom na <a
                    href="mailto:{{city()->contactEmail()}}">{{city()->contactEmail()}}</a>.
        </p>
        <br>
        <br>
        <br>
        <br>
        @if(auth()->guest())
            @include('subscribe_popup')
        @endif
    </div>
@endsection