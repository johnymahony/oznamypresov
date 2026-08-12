@extends('layouts.frontend')

@section('content_frontend')
    <script src="{{url("js/progress-bar.js")}}"></script>
    <link href="{{url('css/progress.css')}}" rel="stylesheet" type="text/css">
    <div class="page">

        <h1 class="h1">Projekty</h1>
        @foreach($projects as $project)
            @include('projects.list_item')
        @endforeach
    </div>
@endsection