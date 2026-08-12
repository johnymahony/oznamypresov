@extends('email.emailInfo')

@section('content')
    Odhlaseny: {{json_encode($cityzen)}}
@endsection