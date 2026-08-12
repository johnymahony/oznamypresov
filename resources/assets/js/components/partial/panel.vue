<template>
    <div class="mb-2 border-solid border-grey-light rounded border mb-4 "
         style="-webkit-box-shadow: 0px 0px 7px 1px rgba(0,0,0,0.3);">
        <div class="bg-grey-lighter px-2 py-3 border-solid border-grey-light border-b bg-gray-200">
            {{title}}
        </div>
        <div class="p-3 bg-white">
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['title'],
        data() {
            return {}
        },
        mounted() {
        },
        methods: {}
    }
</script>
