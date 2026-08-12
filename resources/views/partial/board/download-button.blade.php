@php
    //?c={{$object->id}}
@endphp
@if($board->category_id != city()->podnetyCategoryId() && $board->attachment_path)
    <a href="{{url('download/'.$board->id)}}"
       target="_blank" class="btn btn-default">
        <i class="fa fa-download"></i>
        Stiahnuť prílohu
    </a>
@endif