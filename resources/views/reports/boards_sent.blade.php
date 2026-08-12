@extends('layouts.frontend')

@section('content_frontend')
    <table class="table-striped table">
        <tr>
            <th>Date</th>
            <th>Name</th>
            <th>Count sent</th>
            <th>Count open boards</th>
            <th>Count open attachments</th>
        </tr>
        @foreach($data as $row)
            <tr>
                <td>{{skDate($row['published_at'])}}</td>
                <td>{{$row['title']}}<br>{{$row['description']}}</td>
                <td>{{$row['count']}}</td>
                <td>{{$row['count_opened_board']}}</td>
                <td>{{$row['count_opened_attachments']}}</td>
            </tr>
        @endforeach
    </table>
@endsection
