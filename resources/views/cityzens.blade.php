@extends('layouts.frontend')

@section('content_frontend')
    @php
        $totalBoardsSent = $totalAttachmentsOpened = $totalEmailsSent = $totalEmailsOpened = 0;
    @endphp
    <h1 class="h1">@lang('Cityzens') - {{\App\Cityzen::count()}} zapísaných</h1>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <table class="table">
        <thead>
        <tr>
            <th scope="col">@lang('Created at')</th>
            <th scope="col">@lang('Notices sent')</th>
            <th scope="col">@lang('Attachments opened')</th>
            <th scope="col">@lang('Emails sent/opened')</th>
            {{--<th scope="col">@lang('Email')</th>--}}
        </tr>
        </thead>
        <tbody>
        @foreach(\App\Cityzen::orderBy('created_at', 'desc')->get() as $cityzen)
            <tr>
                @php
                    $boardsSent = $cityzen->boards()->count();
                    $attachmentsOpened = \App\TrackedAttachment::whereCityzenId($cityzen->id)->count();
                    $sent = \App\TrackedEmail::whereCityzenId($cityzen->id)->count();
                    $opened = \App\TrackedEmail::whereCityzenId($cityzen->id)->whereColumn('created_at', '!=', 'updated_at')->count();

                    $totalBoardsSent += $boardsSent;
                    $totalAttachmentsOpened += $attachmentsOpened;
                    $totalEmailsSent += $sent;
                    $totalEmailsOpened += $opened;
                @endphp
                <td style="width: 150px;">
                    <a href="{{url('/')}}/verify/{{$cityzen->hash}}">
                        {{skDateTime($cityzen->created_at)}}
                    </a>
                </td>
                <td style="width: 150px;">{{$boardsSent}}</td>
                <td style="width: 150px;">{{$attachmentsOpened}}
                    - {{$boardsSent ? twoDecimals($attachmentsOpened/$boardsSent*100) : 0}}%
                </td>

                <td style="width: 150px;">{{$sent}} / {{$opened}} - {{$sent ? twoDecimals($opened/$sent*100) : 0}}%</td>
            </tr>
        @endforeach
        <tr>
            <th scope="col">Total</th>
            <th scope="col">{{$totalBoardsSent}}</th>
            <th scope="col">{{$totalAttachmentsOpened}}
                - {{$totalBoardsSent ? twoDecimals($totalAttachmentsOpened/$totalBoardsSent*100) : 0}}%
            </th>
            <th scope="col">{{$totalEmailsSent}} / {{$totalEmailsOpened}}
                - {{$totalEmailsSent ? twoDecimals($totalEmailsOpened/$totalEmailsSent*100) : 0}}%
            </th>
        </tr>
        </tbody>
    </table>
@endsection
