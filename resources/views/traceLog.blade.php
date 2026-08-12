<div class='clear'></div>
@php $name = getModelName($object); @endphp
<button type="button" class='btn btn-default' onclick="$('#traceLog{{ $name }}').load('{{ url('/tracelog/'.$name.'/'.$object->id) }}');">@lang("Trace Log $name")</button>
<div id='traceLog{{ $name }}'></div>