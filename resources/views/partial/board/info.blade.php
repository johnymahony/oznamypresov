<a href="{{$board->url()}}" class="hover-link">
    <h2 class="leading-snug">{{$board->description}}</h2>
</a>
<p class="text-sm mt-2 mb-6"><span class="text-gray-500">{{skDate($board->published_at)}}</span> / @include('partial.category-link')</p>