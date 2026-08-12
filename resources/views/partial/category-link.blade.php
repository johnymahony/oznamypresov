@php
    $label = $board->category_name ? $board->category_name : $board->category->name;
@endphp

<a href="/filter/{{toSlug($label)}}" class="hover-link">{{$label}}</a>