<div id="edit-map">
    <edit-map></edit-map>
</div>

<script>
    var streets = {!! json_encode(\App\Street::toJavascript()) !!}
    var board = {!! $board->toJavascript() !!};
    $(document).ready(() => {
        var mapVueElement = new Vue({
            el: '#edit-map'
        });
    })
</script>