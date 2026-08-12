@extends('layouts.frontend')

@section('content_frontend')

    <table border="1" class="m-20 table table-striped table-bordered">
        @foreach(cities() as $cityClass)
            @php $city = resolve($cityClass) @endphp
            <tr>
                <td><a href="https://{{$city->domain()}}" target="_blank">{{$city->humanName()}}:</a></td>
                <td>{{file_get_contents('https://'.$city->domain().'/stats')}}</td>
            </tr>
        @endforeach
    </table>
@endsection