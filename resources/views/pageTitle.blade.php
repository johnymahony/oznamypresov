@if(request()->kategoria)
    @php
        $category = \App\Category::whereSlug(request()->kategoria)->first();
        $title = $category->name.' - '.city()->humanName();
    @endphp

    @section('pageTitle', $title)
@endif