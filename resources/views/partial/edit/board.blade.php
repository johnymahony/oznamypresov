<div class="card-body">
    @if($board->category_id === city()->podnetyCategoryId())
        @include('partial.edit.readonly', [
            'label' => 'Typ podnetu',
            'id' => 'iniciative_type',
            'value' => $board->iniciative_type
        ])
        @include('partial.edit.readonly', [
            'label' => 'Názov',
            'id' => 'title',
            'value' => $board->title
        ])
    @endif

    @include('partial.edit.readonly', [
        'label' => 'Popis',
        'id' => 'description',
        'value' => $board->description
    ])

    @include('partial.edit.readonly', [
        'label' => 'Zverejnené',
        'value' => skDate($board->published_at)
    ])

    @include('partial.edit.readonly', [
        'label' => 'Adresa',
        'value' => $board->address
    ])

    @if($board->sent_daily)
        @include('partial.edit.readonly', [
            'label' => 'Odoslané',
            'value' => $board->cityzens()->count().' x'
        ])
    @endif
    @include('partial.edit.readonly', [
        'label' => 'Hotové dňa',
        'value' => skDateTime($board->ready_at)
    ])
    <div class="edit-form-input">
        <label for="cadaster_number">Kat. územie</label>
        <div class="">
            <select class="custom-select selectize-input" id="cadaster_number" name="cadaster_number">
                @php
                    $cadasterNumbers = cadasters();
                @endphp
                @foreach($cadasterNumbers as $cadasterNumber => $label)

                    <option value="{{$cadasterNumber}}"
                            @if($board->cadaster_number == $cadasterNumber) selected="selected" @endif>{{$label}}
                        - {{$cadasterNumber}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="edit-form-input">
        <label for="project_id">Projekt</label>
        <div class="">
            <select class="custom-select selectize-input" id="project_id" name="project_id">
                <option value="">[žiadny]</option>
                @foreach(\App\Project::orderBy('name')->get() as $project)
                    <option value="{{$project->id}}"
                            @if($board->project_id == $project->id) selected="selected" @endif>{{$project->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="edit-form-input">
        <label for="comment">Poznámka</label>
        <div class="5">
            <input name="comment" type="text" class="selectize-input" id="comment"
                   value="{{$board->comment}}">
        </div>
    </div>
    <div class="edit-form-input">
        <input type="checkbox" class="form-check-input" id="whole_city" name="whole_city" value="1"
               @if($board->whole_city) checked="checked" @endif>
        <label class="form-check-label" for="whole_city">Celé mesto</label>
    </div>
    <div></div>
    {{--<div class="form-check form-check-inline">--}}
    {{--<input type="checkbox" class="form-check-input" id="not_important" name="not_important"--}}
    {{--@if($board->not_important) checked="checked" @endif>--}}
    {{--<label class="form-check-label" for="not_important">Málo dôležité</label>--}}
    {{--</div>--}}
    <div class="edit-form-input">
        <label for="status" class="">Stav</label>
        <div class="flex justify-between items-center">
            <select class="custom-select selectize-input" id="status" name="status" style="width: 250px;">
                <option value="new"
                        @if($board->status == 'new') selected="selected" @endif>@lang('new')</option>
                <option value="ready"
                        @if($board->status == 'ready') selected="selected" @endif>@lang('ready')</option>
                <option value="done"
                        @if($board->status == 'done') selected="selected" @endif>@lang('done')</option>
            </select>
            <button type="button" class="btn btn-default" onclick="help({{$board->id}})"><i
                        class="fas fa-question-circle"></i> Pomoc <i class="fas fa-question-circle"></i>
            </button>
        </div>
    </div>
    {{csrf_field()}}
    <p class="flex justify-between">
        @include('components.button', [
            'label' => 'Uložiť',
            'type' => 'submit',
            'classes' => 'btn-red'
        ])
        @include('components.button', [
            'label' => 'Celé mesto',
            'type' => 'button',
            'onclick' => 'wholeCity();done();',
            'classes' => 'btn-green'
        ])
        @include('components.button', [
            'label' => 'Hotovo',
            'type' => 'button',
            'onclick' => 'done();',
            'classes' => 'btn-green'
        ])

        <a href="{{url('edit/'.($next ? $next->id : ''))}}"
           style="visibility: @if($next) visible @else hidden @endif">
            @include('components.button', [
                'label' => '>> nasled. >>',
                'classes' => 'bg-orange-300'
            ])
            <input type="hidden" value="{{($next ? $next->id : '')}}" name="next_id">
            <input type="hidden" value="0" name="redirect" id="redirect">
        </a>

    </p>
    <div class="clearfix"></div>
</div>