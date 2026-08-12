@extends('email.emailInfo')

@section('content')
    <p style="line-height: 24px; margin-bottom:15px;">

        Dobrý deň,

    </p>
    <p style="line-height: 24px;margin-bottom:15px;">
        na portáli {{url('/')}} pribudla nová kategória oznamov: <b>{{$category->name}}</b>
    </p>
    <p style="line-height: 24px;margin-bottom:15px;">
        Ak si neprajete odoberať oznamy z tejto kategórie, upravte si nastavenia odoberania kliknutím na túto linku.
    </p>
    @include('settings_link')
@endsection