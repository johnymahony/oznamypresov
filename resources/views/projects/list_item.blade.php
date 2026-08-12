<div class="rounded p-4 bg-gray-100 mb-1">
    <a href="{{url('projekt/'.$project->id)}}">

    <h2>{{$project->name}}</h2>
    {{--<div class="progress">--}}
        {{--<div class="progress-bar" role="progressbar" style="width: {{$percent}}%;"--}}
{{--             aria-valuenow="{{$project->phase_id? $project->phase_id : '0'}}" aria-valuemin="0"--}}
{{--             aria-valuemax="8">{{$project->phase_id? $project->phase->name : ''}}</div>--}}
    {{--</div>--}}

    <div class="progress-bar-wrapper{{$project->id}}"></div>
    </a>
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