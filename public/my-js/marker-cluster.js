var markerLayer;
var map;
(function ($) {
    "use strict";

    $(document).ready(function () {
        map = L.map('mapid').setView([city.latitude, city.longitude], city.mapZoom);

        L.tileLayer(
            // 'https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw',
            'https://{s}.tile.thunderforest.com/atlas/{z}/{x}/{y}{r}.png?apikey=3829712dfc504389a78b1ae9c3d15c82',
            {
                maxZoom: 18,
                id: 'mapbox/streets-v11',
            }).addTo(map);

        // map.on('click', addMarker);


        markerLayer = L.markerClusterGroup();
        map.addLayer(markerLayer);
    });

})(jQuery);

window.addMarkerToCluster = function (lat, lon, id, popup) {
    let marker = L.marker([lat, lon], {title: id, draggable: false});
    if (popup) {
        marker.bindPopup(popup);
    }
    markerLayer.addLayer(marker);
    window['marker' + id] = marker;
    return marker;
};
window.addPolygonToCluster = function (gps, id, popup) {
    console.log('addPolygonToCluster', gps);
    let polygon = L.polygon(gps, {color: 'green'}).bindPopup(popup);
    polygon.getCenter();
    markerLayer.addLayer(polygon);
    // let marker = L.marker([lat, lon], {title: id, draggable: false}).
    // bindPopup('' + id + '| '+popup);
    // markerLayer.addLayer(marker);
    // window['marker' + id] = marker;
};