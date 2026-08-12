@extends('layouts.frontend')

@section('content_frontend')
    <div class="page mt-6">
        @include('components.panel', [
        'content' => view('partial.login'),
        'title' => __('Login')
        ])
    </div>
@endsection
