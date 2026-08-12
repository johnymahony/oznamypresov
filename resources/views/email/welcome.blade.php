@extends('email.emailInfo')

@section('content')
    <p>
        Vitajte.
    </p>
    <p>
        Aby ste dostávali tie správne oznamy, je potrebné nastaviť oblasť kde bývate, resp. oblasť, o ktorú máte záujem.
    </p>
    <p>
        Pre nastavenie posielania správ kliknite na tento odkaz:
    </p>
    @include('settings_link')
@endsection