<template>
    <div>
        <h2 class="h2 py-4">Vyberte ulicu a číslo domu:</h2>
        <div class="flex">
            <div class="w-3/4 pr-4">
                <select v-model="street" class="form-control" id="street"
                        onchange="loadStreetNumbersSelectize($(this).val())">
                    <option value="">[vyberte]</option>
                    <option v-for="street in streets" :value="street.name">{{street.name}}</option>
                </select>
            </div>
            <div class="w-1/4">
                <select name="number" id="number" class="selectize-input" v-model="number">
                    <option value="" v-if="numbers.length === 0">[vyberte ulicu]</option>
                    <option value="" v-else>[číslo]</option>
                    <option v-for="number in numbers"
                            :value="number.id">
                        {{number.co ? number.co + ' / ':''}} {{number.cs}}
                    </option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                    </svg>
                </div>
            </div>

        </div>
        <div class="py-4">
            Ak ste svoju adresu nenašli v zozname ulíc, <a :href="'/no-address?hash='+cityzen.hash"
                                                           class="text-blue-600 underline">kliknite sem</a>
        </div>
        <div v-if="street && number" class="text-center">
            <button class="bg-red-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full" @click="save">
                Pokračovať
            </button>
        </div>
    </div>
</template>

<script>
    import loadAddress from './../mixins/loadAddress';

    export default {
        props: ['cityzen'],
        mixins: [loadAddress],
        data() {
            return {
                street: cityzen.street_id ? cityzen.street.name : '',
                number: cityzen.street_id,
            }
        },
        mounted() {
            console.log('address mounted', this.street, this.cityzen);
            if (this.street) {
                this.loadStreetNumbers();
            }
        },
        methods: {
            save() {
                this.$emit('savePartial', {
                        method: 'SaveAddressFromGeo',
                        payload: {
                            number: this.number,
                            cityzen_id: this.cityzen.id,
                        }
                    }
                );
            },
        }
    }
</script>
