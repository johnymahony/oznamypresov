@php
    if(isset($allBoards)) {
        $boards = $allBoards;
    }
@endphp
<script src="{{url('my-js/marker-cluster.js')}}?v=124"></script>
<style>
    #mapid {
        width: 100%;
    }
</style>
<script>
    let boards = {!! json_encode($boards) !!}
    $(document).ready(function () {
        setTimeout(function () {
            let markers = [];
            let marker;
            for (let board of boards) {
                marker = addMarkerToCluster(board.latitude, board.longitude, board.id);
                marker.bindPopup("" + board.id);
                marker.on('click', function () {
                    loadMarkerPopup(this);
                });
                markers.push(marker);
            }

            var group = new L.featureGroup(markers);
            if (markers.length > 0) {
                try {
                    let bounds = group.getBounds();
                    console.log('bounds', bounds);
                    map.fitBounds(bounds);
                } catch (e) {
                    console.log(e);
                }
            }
        }, 500);
    });

</script>
