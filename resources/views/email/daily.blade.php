@extends('email.emailInfo')

@section('content')
    <div style="line-height: 24px;margin-bottom:15px;">
        @if(!$object->isSetUp())
            <p style="margin-right:2px;">
                Keďže ste si zatial nenastavili oblasť záujmu, budú Vám chodiť všetky oznamy. Pre nastavenie odoberania
                kliknite na tento link: @include('settings_link')
            </p>
            <br>
            <br>
            @endif
            </p>
            <hr>
            @foreach($boards as $board)

                <h2 style="font-size:35px" align="center">{{$board->title}}</h2>
                <p style="font-size:12px; font-style:italic;">{{skDate($board->published_at)}}
                    / {{$board->category->name}}</p>

                <p>{{$board->description}}</p>
                <br>
                @if($board->note_until && !preg_match("/0000/", $board->note_until) && !preg_match("/1970/", $board->note_until))
                    <p style="color: #ff4242; font-size: 20px">! Možnosť pripomienkovať
                        do: <u>{{skDate($board->note_until)}}</u> !</p>
                @endif
                <br>
                @if($board->category_id === city()->podnetyCategoryId())
                    Stav riešenia: {{$board->solution_state}}<br>
                    Riešiteľ: {{$board->person}}<br>
                    Riešenie: {{$board->solution}}<br>
                    Dátum riešenia: {{$board->solution_date ? skDate($board->solution_date) : ''}}<br>
                @endif

                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0"
                                   class="btn btn-primary">
                                <tbody>
                                <tr>
                                    <td align="center">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="10">
                                            <tbody>
                                            <tr class="menu-button">
                                                <td><a href="{{$board->url()}}?t={{$object->id}}" target="_blank">Otvoriť
                                                        celý oznam</a></td>
                                                <td><a href="{{$board->url(['share' => 1])}}" target="_blank">Zdieľať na
                                                        Facebook</a></td>
                                                <td>
                                                    @if($board->category_id != city()->podnetyCategoryId() && $board->attachments->first())
                                                        <a href="{{url('download/'.$board->id)}}?c={{$object->id}}&path={{$board->attachments->first()->path}}"
                                                           target="_blank">Stiahnuť prílohu</a></td>
                                                @endif
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <p class="describe">
                                @if($board->reason == \App\Board::MATCH_POINT)
                                    Túto správu ste dostali, pretože spadá do Vami vyznačenej oblasti záujmu
                                    ({{$object->radius}}m).
                                @endif
                                @if($board->reason == \App\Board::MATCH_STREET)
                                    Túto správu ste dostali, lebo sa týka ulice (ulíc), ktorú ste si vybrali na odber
                                    ({{$board->streets}}).
                                @endif
                                @if($board->reason == \App\Board::MATCH_CITY)
                                    Túto správu ste dostali, lebo sa týka celého mesta a spadá do kategórie ktorú ste si
                                    vybrali na
                                    odber ({{$board->category->name}}).
                                @endif
                                @if($board->reason == \App\Board::MATCH_POINT_NOT_SET_UP)
                                @endif</p>
                            <hr>
                            @endforeach
                            <br>
                            <p>
                                Ak si chcete zmeniť nastavenia zasielania, kliknite na tento
                                link: @include('settings_link')
                            </p>
                        </td>
                    </tr>
                </table>
    </div>
@endsection