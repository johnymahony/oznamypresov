export default {
    data() {
        return {
            map: null,
            drawnItems: null,
            layer_ulice_1: null,
            streetNames: {},
            streetNamesPolylines: {},
            streetSelectize: null,
        }
    },
    methods: {
        initMap() {
            this.loadStreetCoords();
            this.drawnItems = new L.FeatureGroup();
            this.map = L.map('mapid').setView([city.latitude, city.longitude], 13);
            this.drawnItems.addTo(this.map);
            L.tileLayer(
                // 'https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw',
                // 'https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw',
                'https://{s}.tile.thunderforest.com/atlas/{z}/{x}/{y}{r}.png?apikey=3829712dfc504389a78b1ae9c3d15c82',
                {
                    maxZoom: 18,
                    id: 'mapbox/streets-v11',
                }).addTo(this.map);
            this.map.addControl(new L.Control.Draw({
                edit: false,
                draw: {
                    featureGroup: this.drawnItems,
                    circle: false,
                    marker: false,
                    polyline: false,
                    polygon: false,
                    rectangle: false,
                    circlemarker: false,
                },

            }));
            if (json_ulice_1) {
                var bounds_group = new L.featureGroup([]);
                this.map.createPane('pane_ulice_1');

                this.layer_ulice_1 = new L.geoJson(json_ulice_1, {
                    attribution: '<a href=""></a>',
                    pane: 'pane_ulice_1',
                });
                bounds_group.addLayer(this.layer_ulice_1);
            }
            this.selectizeStreets();
            vueEventBus.$on('selectizeRemove', data => {
                this.streetSelectize[0].selectize.removeItem(data.value);
                data.jqueryElement.parent().remove()
            });
            vueEventBus.$on('zoomToStreet', streetName => {
                this.map.fitBounds(this.streetNamesPolylines[streetName].getBounds());
            });

        },
        unDrawStreet(streetName) {
            this.drawnItems.removeLayer(this.streetNamesPolylines[streetName]);
        },
        drawStreet(streetName) {
            console.log('Draw street: ' + streetName);
            if (_.isEmpty(this.streetNames[streetName])) {
                console.log('Missing street: ' + streetName);
                return;
            }
            var polyline = L.polyline(this.streetNames[streetName], {color: 'red'});
            this.streetNamesPolylines[streetName] = polyline;
            console.log(this.streetNamesPolylines);
            this.drawnItems.addLayer(polyline);
        },
        loadStreetCoords() {
            if (!json_ulice_1) {
                return;
            }
            for (var s in json_ulice_1.features) {
                var street = json_ulice_1.features[s];
                for (var x in street.geometry.coordinates) {
                    let line = street.geometry.coordinates[x];
                    let lineConverted = [];
                    for (var xx in line) {
                        lineConverted[lineConverted.length] = [
                            line[xx][1],
                            line[xx][0],
                        ];
                    }
                    if (_.isEmpty(this.streetNames[street.properties['Názov ulice']])) {
                        this.streetNames[street.properties['Názov ulice']] = [];
                    }
                    this.streetNames[street.properties['Názov ulice']][x] = lineConverted;
                }
            }
        },
        addMarkerToMap(lat, lon, id) {
            let marker = L.marker([lat, lon], {
                title: id,
                draggable: false
            }).bindPopup('' + id + '| ' + lat + ', ' + lon);
            console.log('addMarkerToMap', marker);
            console.log('addMarkerToMap', this.drawnItems);
            this.drawnItems.addLayer(marker);
            window['marker' + id] = marker;
            return marker;
        }
        ,
        addToLatLng(dx, dy, lat, lng) {
            //Earth’s radius, sphere
            let R = 6378137;

            //offsets in meters
            let Pi = Math.PI;
            //Coordinate offsets in radians
            let dLat = dy / R;
            let dLon = dx / (R * Math.cos(Pi * lat / 180));
            //OffsetPosition, decimal degrees
            let latO = lat + dLat * 180 / Pi;
            let lonO = lng + dLon * 180 / Pi;
            return [latO, lonO];
        },
        selectizeStreets() {
            console.log('selectizeStreets');
            if($('#tags').length === 0) {
                return;
            }
            this.streetSelectize = $('#tags').selectize({
                delimiter: ',',
                persist: false,
                valueField: 'tag',
                labelField: 'tag',
                searchField: 'tag',
                options: tags,
                onItemAdd(value, $item) {
                    console.log('onItemAdd', value, $item);
                },
                create: false,
                render: {
                    item: function (item, escape) {
                        return '<div class="item" data-value="' + item.tag + '">' +
                            ' <i class=\'fa fa-eye\' aria-hidden=\'true\'  onclick=\'zoomToStreet("' +
                            item.tag + '")\'></i> ' +
                            item.tag +
                            ' <i class=\'fa fa-times\' aria-hidden=\'true\'  onclick=\'selectizeRemove($(this), "' +
                            item.tag + '")\'></i></div>';
                    },
                },
            });
            this.streetSelectize[0].selectize.on('item_add', item => {
                console.log('item', item);
                this.drawStreet(item);
            });
            this.streetSelectize[0].selectize.on('item_remove', item => {
                console.log('item', item);
                this.unDrawStreet(item);
            });
        }
    }
}