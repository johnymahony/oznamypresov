@extends('layouts.frontend')

@section('content_frontend')
    @parent
    <script src="{{ url('leaflet.draw.js') }}"></script>
    <script src="{{url('my-js/street-intersect.js')}}?v=2"></script>
    @if(city()->hasGeoAddresses())
        <script src="{{url('my-js/'.city().'_ulice_1.js')}}"></script>
    @else
        <script>var json_ulice_1 = false</script>
    @endif
    <script src="{{url('my-js/geojson-utils.js')}}"></script>
    <script src="{{url('js/proj4.js')}}"></script>
    @include('google-places')
    <link href="{{url('leaflet.draw.css')}}" rel="stylesheet" type="text/css">


    <script>

        function done() {
            $('#redirect').val(1);
            $('#status').val('done');
            $('#edit-form').submit();
        }

        function wholeCity() {
            console.log('wholeCity');
            $('#whole_city').prop('checked', 'checked');
            vueEventBus.$emit('removeAllStreets');
        }

        $(document).ready(function () {
            document.onkeyup = function (e) {
                console.log('Key pressed', e.which, e);
                if (e.altKey && e.key === 'p') {//P
                    vueEventBus.$emit('addParcelNumber', {number:''});
                }
                if (e.altKey && e.key === 'o') {//O
                    vueEventBus.$emit('addParcelsString');
                }
                if (e.altKey && e.key === 'h') {//O
                    done();
                }
                if (e.altKey && e.key === 'n') {//O
                    $('#next-button').click();
                }
                if (e.altKey && e.key === 'm') {//O
                    wholeCity();

                }
            };
        });
        var tags = [
                @foreach (\Conner\Tagging\Model\Tag::all() as $tag)
            {
                tag: "{{$tag->name}}",
            },
            @endforeach
        ];
        var cadasters = {!! json_encode(cadasters()) !!}

    </script>
    <form method="post" id="edit-form" class="m-4">
        {{--<a href="{{url('navod.pdf')}}" target="_blank">Návod.pdf</a>--}}
        <div class="flex flex-col md:flex-row">
            <div class="w-full md:w-1/3 md:pr-4">
                @include('components.panel', [
                    'content' => view('partial.edit.board', [
                        'next' => $next,
                    ]),
                    'title' => $board->category->name
                ])
                @include('components.panel', [
                    'content' => view('partial.edit.keys'),
                    'title' => 'Klávesové skratky'
                ])
            </div>
            <div class="w-full md:w-2/3">
                @include('partial.edit.map')
            </div>
        </div>

        @if($board->attachments->count() > 0)
            <hr>
            <iframe src="{{url('/download/'.$board->id.'/inline')}}" class="pdf-iframe">


            </iframe>
            <h2>OCR Rozpoznaný text:</h2>
            <pre id="plain-text" onmousedown="if($('#selection-popup').length > 0) $('#selection-popup').remove();">
                {{$board->attachments->first()->plain_text}}
            </pre>
        @endif
    </form>
    @include('traceLog', ['object' => $board])
    <script>
        $(document).ready(function () {
            document.querySelector('#description').onmouseup = drawBorderAroundSelection;
            @if($board->attachments->count() > 0)
            document.querySelector('#plain-text').onmouseup = drawBorderAroundSelection;
            @endif
        });
    </script>
@endsection