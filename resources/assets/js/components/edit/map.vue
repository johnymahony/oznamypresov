<template>
    <div>
        <panel :title="board.title +' '+board.description">
            <div class="flex flex-col md:flex-row" style="margin-right: 0px;">
                <label for="street" class="md:w-1/12">Bod:</label>
                <div v-if="city.has_geo_addresses" class="md:w-11/12 flex">
                    <div class="md:w-7/12">
                        <select v-model="street" class="form-control" id="street"
                                onchange="loadStreetNumbersSelectize($(this).val())">
                            <option value="">[vyberte]</option>
                            <option v-for="street in streets" :value="street.name">{{street.name}}</option>
                        </select>
                    </div>
                    <div class=" px-6 md:w-4/12">
                        <select name="number" id="number" class="selectize-input" v-model="number">
                            <option value="" v-if="numbers.length === 0">[vyberte ulicu]</option>
                            <option value="" v-else>[číslo]</option>
                            <option v-for="number in numbers"
                                    :value="number.id">
                                {{number.cs}} {{number.co ? ' / '+number.co :''}}
                            </option>
                        </select>
                    </div>
                     <button type="button"
                        class="md:w-1/12 bg-gray-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full"
                        @click="savePointFromAddress">
                    Uložiť
                </button>
                </div>
                <div v-else class="md:w-11/12 flex">
                    <vue-google-autocomplete
                            id="map"
                            classname="selectize-input"
                            placeholder="Začnite písať adresu bodu"
                            :country="['sk']"
                            v-on:placechanged="getAddressData"
                    />
                </div>
            </div>
            <br>
            <div id="mapid" style="width: 100%; height: 500px;"></div>
            <div class="form-group row" style="margin: 10px 0px;">
                <label for="tags" class="col-sm-2 col-form-label">Ulice:</label>
                <div class="col-sm-10">
                    <input type="text" name="tags" id="tags" class="form-control"
                           :value="board.selected_streets.join(',')">
                </div>
            </div>
            <button class="btn btn-light" @click="removeAllStreets" type="button">zrušiť všetky ulice</button>
        </panel>
        <parcelList :board="board"></parcelList>
    </div>
    <!--<div class="w-full md:w-1/3">-->
    <!---->
    <!--</div>-->
</template>

<script>
    import loadAddress from './../../mixins/loadAddress';
    import map from './../../mixins/map';
    import parcelList from './parcel-list';
    import panel from './../partial/panel';
    import VueGoogleAutocomplete from 'vue-google-autocomplete'

    export default {
        mixins: [loadAddress, map],
        components: {parcelList, panel, VueGoogleAutocomplete},
        data() {
            return {
                json_ulice_1: json_ulice_1,
                city: city,
                board: board,
                street: '',
                number: '',
                streets_selected: '',
            }
        },
        mounted() {
            console.log('edit map mounted');
            this.initMap();
            this.map.addControl(new L.Control.Draw({
                edit: {
                    featureGroup: this.drawnItems,
                    poly: {
                        allowIntersection: false,
                    },
                },
                draw: {
                    polygon: {
                        allowIntersection: false,
                        showArea: true,
                    },
                    circle: false,
                    circlemarker: false,
                    polyline: false,
                },
            }));
            this.map.on(L.Draw.Event.CREATED, event => {
                console.log('map.on(L.Draw.Event.CREATED', event);
                let layer = event.layer;
                switch (event.layerType) {
                    case 'marker':
                        this.savePoint(layer._latlng.lat, layer._latlng.lng).done(point => {
                            this.addMarkerToMap(layer._latlng.lat, layer._latlng.lng, point.id);
                            toast('Bod uložený.');
                        });
                        return;
                    case 'rectangle':
                    case 'polygon':
                        layer.setStyle({
                            color: 'green',
                        });
                        this.addShape(layer)
                        ;
                        break;
                }
                this.drawnItems.addLayer(layer);
            });
            this.map.on('draw:deleted', e => {
                console.log('map.on(draw:deleted', e);
                var layers = e.layers;
                layers.eachLayer(layer => {
                    console.log('layers.eachLayer(function (layer)', layer);
                    if (_.isEmpty(layer._popup)) {
                        return;
                    }
                    let id = layer._popup._content.split('|')[0];
                    if (!_.isEmpty(layer.editing._marker)) {
                        this.deletePoint(id);
                    } else {
                        this.deleteParcel(id);
                    }
                });
            });

            for (let point of this.board.points) {
                this.addMarkerToMap(point.latitude, point.longitude, point.id);
            }
            for (let parcel of this.board.parcels) {
                if (parcel.points && parcel.points.match(/\[/)) {
                    let polygon = L.polygon(JSON.parse(parcel.points), {color: 'green'});
                    this.drawnItems.addLayer(polygon);
                    polygon.bindPopup(parcel.id + '|' + parcel.number).openPopup();
                    window['parcela' + parcel.id] = polygon;
                }
            }
            for (let street of this.board.selected_streets) {
                this.drawStreet(street);
            }
            vueEventBus.$on('addLayer', layer => {
                this.drawnItems.addLayer(layer);
            });
            vueEventBus.$on('removeAllStreets', this.removeAllStreets);
            console.log('#edit map end');
        },
        methods: {
            getAddressData(addressData, placesData) {
                console.log('address, data', addressData, placesData);
                this.savePointOnMap({
                    lat: addressData.latitude,
                    lng: addressData.longitude,
                });
                // this.address = addressData.route + ' ' + addressData.street_number;
                // this.addressData = addressData;
                // this.placesData = placesData;
            },
            removeAllStreets() {
                this.streetSelectize[0].selectize.clear();
                for (var i in this.streetNamesPolylines) {
                    this.unDrawStreet(i);
                }
            },
            deletePoint(pointId) {
                let pp = window['marker' + pointId];
                console.log(pp);
                ajaxRequest('DeletePoint', {point_id: pointId}).done(() => {
                    if (!_.isEmpty(pp)) {
                        this.map.removeLayer(pp);
                    }
                });
            },
            deleteParcel(parcelId) {
                console.log('deleteParcel', parcelId);
                let pp = window['parcela' + parcelId];
                console.log(pp);
                if (!_.isEmpty(pp)) {
                    this.map.removeLayer(pp);
                }
                ajaxRequest('DeleteParcel', {parcel_id: parcelId}).done(() => {
                    this.board.parcels = this.board.parcels.filter(p => p.id !== parcelId);
                });
            },
            savePointFromAddress() {
                this.savePointOnMap({
                    street_id: this.number,
                });
            },
            savePointOnMap(params) {
                params.board_id = this.board.id;
                ajaxRequest('AddPoint', params).done(point => {
                    this.addMarkerToMap(point.latitude, point.longitude, point.id);
                    this.zoomTo(point.latitude, point.longitude);
                    toast('Adresný bod uložený');
                });
            },
            savePoint(lat, lng) {
                var deferred = $.Deferred();
                ajaxRequest('AddPoint', {
                    board_id: this.board.id,
                    lat: lat,
                    lng: lng,
                }).done(function (point) {
                    deferred.resolve(point);
                });
                return deferred.promise();
            },
            addShape(shape) {
                ajaxRequest('AddParcel', {
                    coords: shape._latlngs[0],
                    board_id: this.board.id,
                }).done(parcel => {
                    parcel.cssClass = 'parcel-found';
                    this.drawShape(shape, parcel)
                    toast('Uložené.');
                });
            },
            drawShape(polygon, parcel) {
                polygon.bindPopup(parcel.id + '|' + parcel.number);
                window['parcela' + parcel.id] = polygon;
                this.board.parcels.push(parcel);
            },
            zoomTo(lat, lng) {
                this.map.panTo(new L.LatLng(lat, lng))
            }
        }
    }
</script>
