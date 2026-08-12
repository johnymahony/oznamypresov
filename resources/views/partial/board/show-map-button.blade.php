@if($board->latitude)
    @include('components.button', [
        'label' => 'Ukázať na mape',
        'onclick' => 'window.scrollTo(0, $(\'#mapid\').position().top-15);panToBoard('.$board->id.');loadMarkerPopup(window[\'marker\'+'.$board->id.'])',
        'icon' => 'fa-eye',
        'classes' => 'btn-info'
    ])
@endif