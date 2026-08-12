<template>
    <div>
        <geo-address v-if="city.has_geo_addresses" :cityzen="cityzen" @savePartial="savePartial"/>
        <google-address v-else :cityzen="cityzen" @savePartial="savePartial"/>
    </div>
</template>

<script>
    import GoogleAddress from './google-address';
    import GeoAddress from './geo-address';

    export default {
        props: ['cityzen'],
        components: {
            GoogleAddress,
            GeoAddress
        },
        mounted() {
        },
        methods: {
            savePartial(data) {
                this.$parent.savePartial(data.method, data.payload);
            }
        }
    }
</script>
