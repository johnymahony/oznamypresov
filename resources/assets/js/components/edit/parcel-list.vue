<template>
    <panel title="Parcely">
        <button type="button" class="btn btn-info" @click="addParcelNumber({number: ''})">
            <i class="fas fa-plus"></i> Pridať číslo parcely
        </button>
        <button type="button" class="btn btn-info" @click="addParcelsString">
            <i class="fas fa-search"></i> Rozoznať čísla parciel
        </button>
        <button type="button" class="btn btn-info" @click="refreshParcels">
            <i class="fas fa-sync-alt"></i> Vyhľadať parcely
        </button>
        <div class="my-4">
            <button type="button" class="btn btn-default parcel-search"
                    @click="deleteParcels('parcel-search')">Zmazať nehľadané
            </button>
            <button type="button" class="btn btn-default not-found"
                    @click="deleteParcels('not-found')">Zmazať nenájdené
            </button>
            <button type="button" class="btn btn-default error-found"
                    @click="deleteParcels('error-found')">Zmazať chýbové
            </button>
            <button type="button" class="btn btn-default parcel-found"
                    @click="deleteParcels('parcel-found')">Zmazať nájdené
            </button>
        </div>
        <div class="mt-6" style="max-height: 500px; overflow: auto">
            <div v-for="parcel in board.parcels"
                 :class="'parcel' + parcel.id + ' parcel-' + parcel.cssClass">
                <input disabled="disabled" type="text" :class="parcel.cssClass+' selectize-input'" :id="'parcel' + parcel.id"
                       style="width: 100px;" :value="parcel.number">
                <button type="button" class='btn btn-default' @click="$parent.deleteParcel(parcel.id)">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
                <button type="button" class='btn btn-default' @click="zoomParcel(parcel.id)">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                </button>
                <button type="button" class='btn btn-default' @click="findParcel(parcel)">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </panel>
</template>

<script>
    import panel from './../partial/panel';

    export default {
        props: ['board'],
        components: {panel},
        data() {
            return {}
        },
        mounted() {
            vueEventBus.$on('addParcelsString', this.addParcelsString);
            vueEventBus.$on('addParcelNumber', this.addParcelNumber);
        },
        methods: {
            addParcelNumber(data) {
                let number = data.number;
                if (_.isEmpty(number)) {
                    number = prompt('Zadajte čislo parcely');
                    if (_.isEmpty(number)) {
                        alert('Neplatné číslo parcely');
                        return;
                    }
                }
                toast('Pridávam a vyhľadávam...');
                ajaxRequest('AddParcelNumber', {
                    board_id: this.board.id,
                    cadasterNumber: data.cadasterNumber,
                    number: number,
                }).done(parcel => {
                    this.findParcel(parcel);
                    this.$parent.board.parcels.push(parcel);
                });
            },
            findParcel(parcel) {
                ajaxRequest('FindParcel', {
                    parcel_id: parcel.id,
                }).done(dbParcel => {
                    let parcelInput = $('#parcel' + parcel.id);
                    parcelInput.removeClass('parcel-search');
                    let parent = parcelInput.parent();
                    parent.parent().removeClass('parcel-parcel-search');
                    if (dbParcel === false) {
                        toast('Chyba pri vyhľadávaní', null, 'alert-danger');
                        parcel.cssClass = 'error-found';
                        return;
                    }
                    if (dbParcel === 'NA') {
                        toast('Parcela nenájdená', null, 'alert-warning');
                        parcel.cssClass = 'not-found';
                        return;
                    }
                    toast('Parcela nájdená a zakreslená');
                    parcel.cssClass = 'parcel-found';
                    let coords = JSON.parse(dbParcel.points);
                    let polygon = L.polygon(coords, {color: 'green'});
                    polygon.bindPopup(parcel.id + '|' + parcel.number);
                    window['parcela' + parcel.id] = polygon;
                    vueEventBus.$emit('addLayer', polygon);
                });
            },
            addParcelsString() {
                let string = prompt('Zadajte text');
                if (_.isEmpty(string)) {
                    return;
                }
                if (_.isEmpty(string)) {
                    alert('Neplatný text');
                    return;
                }
                toast('Pridávam a vyhľadávam...');
                ajaxRequest('AddParcelNumberFromString', {
                    board_id: this.board.id,
                    string: string,
                }).done(parcels => {
                    if (parcels.length === 0) {
                        toast('Žiadne parcely nenájdené', null, 'alert-danger');
                        return;
                    }
                    window.location.reload();
                });
            },
            refreshParcels() {
                ajaxRequest('RefreshParcels', {
                    board_id: this.board.id,
                }).done(parcels => {
                    window.location.reload();
                });
                toast('Vyhľadávam, prosím čakajte', 30000, 'alert-warning');
            },
            zoomParcel(parcelId) {
                let pp = window['parcela' + parcelId];
                if (_.isEmpty(pp)) {
                    toast('Táto parcela v mape neexistuje', null, 'alert-danger');
                    return;
                }
                console.log(pp);

                this.$parent.zoomTo(pp._latlngs[0][0].lat, pp._latlngs[0][0].lng);
                ;
            },
            deleteParcels(status) {
                let deleted = this.board.parcels.filter(p => p.cssClass === status);
                console.log('deletingParcels', deleted);
                deleted.map(p => {
                    this.$parent.deleteParcel(p.id);
                })
            }
        }
    }
</script>
