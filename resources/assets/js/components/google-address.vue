<template>
    <div>
        <h2 class="h2 py-4">Vyberte ulicu a číslo domu:</h2>
        <vue-google-autocomplete
                id="map"
                classname="selectize-input"
                placeholder="Tu začnite písať vašu adresu"
                :country="['sk']"
                v-on:placechanged="getAddressData"
        >
        </vue-google-autocomplete>
        <div v-if="address" class="text-center mt-4">
            <button class="bg-red-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full" @click="save">
                Pokračovať
            </button>
        </div>
    </div>
</template>

<script>
    import VueGoogleAutocomplete from 'vue-google-autocomplete'

    export default {
        props: ['cityzen'],
        components: {
            VueGoogleAutocomplete
        },
        data() {
            return {
                address: false,
            }
        },
        mounted() {
            console.log('address mounted', this.street, this.cityzen);
        },
        methods: {
            save() {
                this.$emit('savePartial', {
                        method: 'SaveGoogleAddress',
                        payload: {
                            cityzen_id: this.cityzen.id,
                            address: this.address,
                            addressData: this.addressData,
                            placesData: this.placesData,
                        }
                    }
                );
            },
            getAddressData(addressData, placesData) {
                console.log('address, data', addressData, placesData);
                this.address = addressData.route + ' ' + addressData.street_number;
                this.addressData = addressData;
                this.placesData = placesData;
            }
        }
    }
</script>
