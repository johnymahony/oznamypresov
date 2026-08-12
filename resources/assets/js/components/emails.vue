<template>
    <div class="p-2">
        <h2 class="h2">Nastavenie súhrnného zasielania</h2>
        <div class="flex py-2">
            <label for="categories" class="w-3/4">Minimálny počet oznamov na zaslanie:</label>
            <div class="w-1/4">
                <input type="number" name="min_emails" id="min_emails" class="selectize-input"
                       v-model="cityzen.min_emails">
            </div>
        </div>
        <p class=" py-4 border-b border-gray-400">*Keď nastavíte napr. 3, tak to znamená, že ak by Vám mal byť odoslaný email s dvomi oznamami, tak sa email
            neodošle ale počká sa do ďalšieho dňa pokiaľ nebudú oznamy tri.</p>
        <div class="flex pt-4">
            <label for="categories" class="w-3/4">Najviac koľko dní sa majú oznamy hromadiť pred
                odoslaním (max {{max}}):</label>
            <div class="w-1/4">
                <input type="number" name="day_emails" id="day_emails" class="selectize-input" :max="max"
                       v-model="cityzen.day_emails">
            </div>
        </div>
        <p class=" py-4">*Oznamy sa vám určite pošlu ak budú staršie ako nastavený počet dní bez ohľadu na nastavenie minimálneho
            počtu.</p>
        <div class="text-center">
            <button class="bg-red-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full" @click="save">Uložiť nastavenia</button>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['cityzen'],
        data() {
            return {
                max: 7
            }
        },
        mounted() {
        },
        methods: {
            save() {
                if (this.cityzen.day_emails > this.max) {
                    alert('Počet dní hromadenia nemôže byť váčší ako ' + this.max);
                    return;
                }
                this.$parent.savePartial('SaveEmails', {
                    cityzen: this.cityzen,
                });
            },
        }
    }
</script>
