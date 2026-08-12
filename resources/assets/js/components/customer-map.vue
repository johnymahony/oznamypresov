<template>
    <div>
        <div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4" role="alert">
            <p class="font-bold">Oblasť záujmu:</p>
            <p>
                <label for="radius" class="inline-block py-4">Vzdialenosť záujmu: <span id="radiusValue">{{radius}}
                    m</span></label>
                <input v-model="radius" type="range" name="radius" id="radius" class="w-full" min="10" max="5000"
                       @change="setRadius" @input="changeRadius"/>
            </p>
        </div>
        <div id="mapid" style="width: 100%; "></div>
        <div v-if="city.has_geo_addresses">
            <h3 class="py-4">Ulice o ktore máte záujem:</h3>
            <input type="text" name="tags" id="tags" class="form-control"
                   :value="cityzen.streets_selected">
        </div>
        <div class="text-center">
            <button class="bg-red-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full my-4" @click="save">
                Pokračovať
            </button>
        </div>
    </div>
</template>

<script>
    import Map from '../mixins/map';

    export default {
        props: ['cityzen'],
        mixins: [Map],
        data() {
            return {
                radius: cityzen.radius,
                polygon: null,
                marker: null,
                panTimeout: null,
            }
        },
        mounted() {
            console.log('map mounted', this.cityzen);
            this.initMap();
            this.map.on('draw:created', e => {
                console.log('map.on(\'draw:created\'', e);
                if (this.polygon) {
                    this.drawnItems.removeLayer(this.polygon);
                }
                if (this.marker) {
                    this.drawnItems.removeLayer(this.marker);
                }
                if (e.layerType === 'marker') {
                    this.marker = e.layer;
                    // $('#street').val('');
                    // $('#number').val('');
                    this.setCityzenPolygon();
                    this.setRadius();
                    streetsCrossCheck(this.layer_ulice_1, this.polygon, this, true);
                }
                this.drawnItems.addLayer(e.layer);
            });
            this.setMarkerFromAddress();
        },
        methods: {
            setMarkerFromAddress() {
                if (this.marker) {
                    this.drawnItems.removeLayer(this.marker);
                }
                if (this.polygon) {
                    this.drawnItems.removeLayer(this.polygon);
                }
                let lat = this.cityzen.latitude;
                let lng = this.cityzen.longitude;
                console.log(lat, lng);
                if (lat) {
                    this.marker = this.addMarkerToMap(lat, lng);
                    this.setCityzenPolygon();
                    streetsCrossCheck(this.layer_ulice_1, this.polygon, this, this.cityzen.first_time);
                    this.map.panTo(new L.LatLng(lat, lng));
                }
            },
            setCityzenPolygon() {
                let lat = this.marker._latlng.lat;
                let lng = this.marker._latlng.lng;
                let radius = this.radius;
                let x1 = this.addToLatLng(radius, radius, lat, lng);
                let x2 = this.addToLatLng(radius, radius * -1, lat, lng);
                let x3 = this.addToLatLng(radius * -1, radius, lat, lng);
                let x4 = this.addToLatLng(radius * -1, radius * -1, lat, lng);
                let cityzenPolygon = [
                    [x4[0], x4[1]],
                    [x2[0], x2[1]],
                    [x1[0], x1[1]],
                    [x3[0], x3[1]],
                ];
                console.log('cityzenPolygon', cityzenPolygon);
                this.polygon = L.polygon(cityzenPolygon, {color: 'green'});
                this.drawnItems.addLayer(this.polygon);
            },
            changeRadius() {
                if (this.polygon) {
                    this.drawnItems.removeLayer(this.polygon);
                }
                if (this.marker) {
                    this.setCityzenPolygon();
                    console.log('marker', this.marker._latlng.lat);
                    clearTimeout(this.panTimeout);
                    this.panTimeout = setTimeout(() => {
                        map.panTo(new L.LatLng(this.marker._latlng.lat, this.marker._latlng.lng));
                    }, 800);
                }
                let radius = this.radius;
                let map = this.map;
                if (radius < 400) {
                    map.setZoom(16);
                } else if (radius > 400 && radius <= 800) {
                    map.setZoom(15);
                } else if (radius > 800 && radius < 1600) {
                    map.setZoom(14);
                } else if (radius >= 1600 && radius < 3000) {
                    map.setZoom(13);
                } else if (radius >= 3000) {
                    map.setZoom(12);
                }
            },
            setRadius() {
                this.changeRadius();
                if (this.polygon) {
                    console.log('SetRadius streetNamesPolylines', this.streetNamesPolylines);
                    for (var x in this.streetNamesPolylines) {
                        this.unDrawStreet(x);
                    }

                    if (this.layer_ulice_1) {
                        streetsCrossCheck(this.layer_ulice_1, this.polygon, this, true);
                        this.streetSelectize[0].selectize.destroy();
                        $('#tags').val(streetNamesI.join(','));
                        this.selectizeStreets();
                    }
                }
            },
            save() {
                this.$parent.savePartial('SaveMap', {
                    radius: this.radius,
                    streets_selected: $('#tags').val(),
                    latitude: this.marker._latlng.lat,
                    longitude: this.marker._latlng.lng,
                    cityzen_id: this.cityzen.id,
                });
            },
        }
    }
</script>
