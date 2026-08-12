@extends('layouts.frontend')

@section('content_frontend')
    <script src="{{url("js/progress-bar.js")}}"></script>
    <link href="{{url('css/progress.css')}}" rel="stylesheet" type="text/css">
    <div style="background: white;border: 1px solid rgba(0,0,0,.125);" class="page">

        <div style="padding: 20px;
    background-color: #f0f8ff;
    border-bottom: 1px solid rgba(0,0,0,.125);"><h1>{{$project->name}}</h1></div>
        <div style="padding: 20px;">
            <div class="progress-bar-wrapper{{$project->id}}"></div>
            <hr>
            <div style="display: flex; justify-content: space-between">
                <img src="{{url('images/city.png')}}"
                     style="margin-top: 10px; padding: 20px 50px; border-radius: 5px;opacity: 0.1; border: 1px solid rgba(0,0,0,.4)"/>
                <div style="max-width: 400px;">
                    <h3 style="text-align: right">Dokumenty:</h3>
                    @foreach($project->attachments as $attachment)
                        <p>
                            <a href="{{$attachment->board->url()}}" target="_blank"><i
                                        class="fas fa-external-link-alt"></i>
                                {{$attachment->board->description}}
                            </a>
                        </p>
                    @endforeach
                </div>
            </div>
        </div>
        <hr>
        @include('partial.map', ['boards' => $project->boards])
        <hr>
        <div id="mapid" class=""></div>
        {{--<img src="{{url('images/forum-mapa.png')}}" style="width: 100%">--}}
    </div>
    <script>

        ProgressBar.singleStepAnimation = 100;
        ProgressBar.init(
            [
                "Nový projekt",
                @foreach(\App\Phase::orderBy('id')->get() as $phase)
                    "{{$phase->name}}",
                @endforeach

            ],
            '{{$project->phase_id ? $project->phase->name : 'Nový projekt'}}',
            'progress-bar-wrapper{{$project->id}}' // created this optional parameter for container name (otherwise default container created)
        );
    </script>
@endsection