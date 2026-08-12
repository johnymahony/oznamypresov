@extends('layouts.frontend')

@section('content_frontend')
    <h1>Zoznam objednávok - {{ $icoInput == 'nezname_ico' ? 'Neznáme IČO' : @$data[0]->{"Dodávateľ"} }}</h1>
    <div>
    <div class="row">
        <div class="col-sm-2">
            <b>Dodávateľ</b>
        </div>
        <div class="col-sm-6">
            <b>Text</b>
        </div>
        <div class="col-sm-2">
            <b>Hodnota</b>
        </div>
        <div class="col-sm-2">
            <b>Dátum_vyhotovenia</b>
        </div>
    </div>
    @php
        $i = 1;
    @endphp
    @foreach($data as $ico => $data)
        <div class="row">
            <div class="col-sm-2">
                ICO: {{ $data->{"IČO"} }}
            </div>
            <div class="col-sm-6">
                {{ $data->{"Text_objednávky"} }}
            </div>

            <div class="col-sm-2">
                {{ $data->{"Hodnota_objednávky"} }} &euro;
            </div>
            <div class="col-sm-2">
                {{ $data->{"Dátum_vystavenia"} }}
            </div>
        </div>
        <hr>
    @endforeach
@endsection