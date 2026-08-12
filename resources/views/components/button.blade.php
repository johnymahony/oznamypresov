@php
    if(!isset($type)) {
        $type = 'button';
    }
    if(!isset($classes)) {
        $classes = '';
    }
    if(!isset($icon)) {
        $icon = false;
    }
@endphp

<button type="{{$type}}" @if(@$onclick)onclick="{!! $onclick !!}"@endif class="{{$classes}} btn">
    @if($icon)
        <i class="fa {{$icon}}"></i>
        @endif
    {!! $label !!}
</button>