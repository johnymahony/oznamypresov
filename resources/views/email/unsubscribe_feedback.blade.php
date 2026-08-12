@extends('email.emailInfo')

@section('content')
    Spätná väzba pri odhlasení z mailing listu: {{$reason}}, {{$comment}}
@endsection