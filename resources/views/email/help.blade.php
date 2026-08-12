@extends('email.emailInfo')

@section('content')
    <p>
        Pomoc k boardu: {{$board->id}} {{$board->description}}<br>
        <a href="{{url('edit/'.$board->id)}}">Otvoriť</a><br>
        Operátor: {{auth()->user()->email}}
    </p>
    <p>
        Správa: {{$message}}
    </p>
@endsection