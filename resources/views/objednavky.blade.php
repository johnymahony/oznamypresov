@extends('layouts.frontend')

@section('content_frontend')
    <h1>Zoznam objednávok</h1>
    <p>Údaje o zverejnených objednávkach pochádzajú z <a href="http://egov.presov.sk/default.aspx?NavigationState=1100:0:">rozhrania pre otvorené dáta mesta Prešov</a>.</p>
    <div>
        <form id="filterForm">
            <div class="form-group row">
                <label for="rok" class="col-sm-3 col-form-label">Rok</label>
                <div class="col-sm-9">
                    <select class="custom-select" id="rok" name="rok" onchange="$('#filterForm').submit()">
                        <option value="">[celé obdobie]</option>
                        @for($i = date('Y'); $i >= 2011; $i--)
                            <option value="{{$i}}"
                                    @if(request()->get('rok') == $i)  selected="selected" @endif>{{$i}}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for=stav" class="col-sm-3 col-form-label">Stav</label>
                <div class="col-sm-9">
                    <select class="custom-select" id="stav" name="stav" onchange="$('#filterForm').submit()">
                        <option value="">[všetky stavy]</option>
                        <option value="Vybavená" @if(request()->get('stav') == 'Vybavená')  selected="selected" @endif>
                            Vybavená
                        </option>
                        <option value="Nevybavená"
                                @if(request()->get('stav') == 'Nevybavená')  selected="selected" @endif>Nevybavená
                        </option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for=organizacia" class="col-sm-3 col-form-label">Organizačná štruktúra úradu</label>
                <div class="col-sm-9">
                    <select class="custom-select" id="organizacia" name="organizacia"
                            onchange="$('#filterForm').submit()">
                        <option value="">[všetky organizacie]</option>
                        @foreach($organizacie as $organizacia)
                            <option value="{{$organizacia}}"
                                    @if(request()->get('organizacia') == $organizacia)  selected="selected" @endif>{{$organizacia}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-sm">
            <b>Dodávateľ</b>
        </div>
        <div class="col-sm">
            <b>Celková hodnota</b>
        </div>
    </div>
    @php
        $i = 1;
    @endphp
    @foreach($data as $ico => $data)
        <div class="row">
            <div class="col-sm">
                <a href="{{url('objednavky').'/'.urlencode($ico).'/'.request()->get('rok').'/'.request()->get('stav')}}">{{$i++}} {{$data['ico']}} {{$ico == 'nezname_ico' ? '' : $data['name']}}</a>
            </div>
            <div class="col-sm">
                {{$data['sum']}} &euro;
            </div>
        </div>
        <hr>
    @endforeach
@endsection