@extends('layouts.frontend')

@section('content_frontend')
    <div class="page">
        {!! session('flash') !!}
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            setCookie('zobrazit-formular', 'nie', 365);
        });
    </script>
    @php
        session(['flash' => null]);
    @endphp
@endsection