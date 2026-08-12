<div class="mb-12 border-b pb-10 border-red-200">
    @include('partial.board.info')
    <a href="{{$board->url()}}" class="btn btn-red">
        <i class="fa fa-external-link-alt"></i> Otvoriť
    </a>
    @include('partial.board.show-map-button')
    @include('partial.board.download-button')
    <span onclick="shareFb('{{$board->url()}}')" class="inline-block hover-link">
        Zdieľať
    </span>
    @if(!auth()->guest())
        <span class="list-status"><b>Stav:</b> @lang($board->status) </span>
        <a href="{{url('/edit/'.$board->id)}}">
            @include('components.button', [
                'label' => 'Zmeniť',
                'classes' => 'edit-button edit-button-'.$board->status,
                'icon' => 'fa-edit'
            ])
        </a>
    @endif
</div>