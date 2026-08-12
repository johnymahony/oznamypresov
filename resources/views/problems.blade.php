@extends('layouts.frontend')

@section('content_frontend')
    <table class="table-auto table-bordered table-striped">
        @foreach(\App\Problem::latest()->with('board')->limit(300)->get() as $problem)
            <tr class="hover:bg-gray-400">
                <td class="p-2">{{$problem->id}}</td>
                <td class="p-2">{{skDateTime($problem->created_at)}}</td>
                <td class="p-2">{{$problem->text}}</td>
                <td class="p-2">@if($problem->board_id)<a href="{{$problem->board->url()}}">{{$problem->board_id}}</a>@endif</td>
                <td class="p-2"><button onclick="if(confirm('Skutočne zmazať problém {{$problem->id}}?')) window.location = '/delete-problem/{{$problem->id}}'">zmazať</button></td>
            </tr>
        @endforeach
    </table>
@endsection