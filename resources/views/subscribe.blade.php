@extends('layouts.frontend')

@section('content_frontend')
    <script src="{{url("js/progress-bar.js")}}"></script>
    <link href="{{url('css/progress.css')}}" rel="stylesheet" type="text/css">
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
    <div id="form" class="page">
        <customer-settings></customer-settings>
    </div>

@endsection

@section('footer_scripts')
    <script>
        var cityzen = {!! $cityzen !!};
        var streets = {!! $streets !!};
        var tags = [
                @foreach (\Conner\Tagging\Model\Tag::all() as $tag)
            {
                tag: "{{$tag->name}}",
            },
            @endforeach
        ];
        $(document).ready(() => {
            Vue.mixin({
                data() {
                    return {
                        categories: {!! $categories !!},
                        city: city
                    }
                },
            });

            new Vue({
                el: '#form',
            })
        })
    </script>
@append