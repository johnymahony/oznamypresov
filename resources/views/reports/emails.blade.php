@extends('layouts.frontend')

@section('content_frontend')
    <table class="table-striped table">
        <tr>
            <th>Date</th>
            <th>Blade</th>
            <th>Count opened</th>
            <th>Attachments opened</th>
            <th>AVG</th>
        </tr>
        @foreach($data as $row)
<tr>
    <td>{{skDate($row['date_sent'])}}</td>
    <td>{{$row['blade']}}</td>
    <td>{{$row['count_opened']}} / {{$row['count_sent']}} - {{twoDecimals($row['count_opened'] / $row['count_sent']*100)}}%</td>
    <td>
        @if($row['blade'] === 'daily')
            {{$row['count_attachments_opened']}} - {{twoDecimals($row['count_attachments_opened'] / $row['count_sent']*100)}}%
        @endif
    </td>

    <td>{{$row['avg']}}</td>
</tr>
        @endforeach
    </table>
@endsection
