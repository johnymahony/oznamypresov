<template>
    <div class="form-group">
        <h2 class="h2 py-4">Kategórie ktoré chcete odoberať:</h2>
        <div class="well">
            <div v-for="category in categories" class="form-check">
                <label class="form-check-label">
                    <input v-model="categories_selected" type="checkbox" class="form-check-input"
                           :value="category.id"
                    > &nbsp;&nbsp;&nbsp;{{category.name}}
                </label>
                <span @click="info(category)"
                      style="display: inline-block; padding: 3px 8px; border-radius: 3px; border: 1px solid lightblue; cursor: pointer">
                    <i class="fa fa-info"></i>
                        </span>
                <div :id="'info'+category.id" style="display: none" class="alert alert-secondary my-2">
                    {{category.info}}
                </div>
            </div>
        </div>
        <div class="text-center my-4">
            <button class="bg-red-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full" @click="save">
                Pokračovať
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['cityzen'],
        data() {
            return {
                opened: {},
                categories_selected: []
            }
        },
        mounted() {
            this.categories_selected = this.cityzen.categories_selected;
        },
        methods: {
            info(category) {
                $('#info' + category.id).toggle();
            },
            save() {
                console.log('cityzen.categories_selected', this.categories_selected);
                this.$parent.savePartial('SaveCategories', {
                    categories_selected: this.categories_selected,
                    cityzen_id: this.cityzen.id,
                });
            },
        }
    }
</script>
