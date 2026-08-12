<template>
    <div class="mt-6 mb-2 border-solid border-grey-light rounded border mb-4 mx-auto"
         style="-webkit-box-shadow: 0px 0px 7px 1px rgba(0,0,0,0.3);max-width: 700px;">
        <div class="bg-grey-lighter px-2 py-3 border-solid border-grey-light border-b bg-gray-200">
            Sprievodca nastavením
            <button class="btn btn-secondary" @click="reset" style="float: right; margin: 0">Nastaviť znovu</button>
            <div style="clear: both"></div>
        </div>
        <div class="p-3 bg-white">
            <div id="customer-progress">
            </div>
            <CityzenAddress v-show="step === 'address'" :cityzen="cityzen"></CityzenAddress>
            <Categories v-show="step === 'categories'" :cityzen="cityzen"></Categories>
            <CustomerMap v-if="step === 'map'" :cityzen="cityzen"></CustomerMap>
            <CustomerEmails v-show="step === 'emails'" :cityzen="cityzen"></CustomerEmails>
            <div v-show="step === 'end'">
                <div class="my-6 bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md"
                     role="alert">
                    <div class="flex">
                        <div class="py-1">
                            <svg class="fill-current h-6 w-6 text-teal-500 mr-4" xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 20 20">
                                <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold">Všetky nastavenia boli úspešne uložené.</p>
                        </div>
                    </div>
                </div>
                <div v-if="!feedback" class="text-center">
                    <!--{{csrf_field()}}-->
                    <h4 class="h4 mb-6">Bolo užívateľské rozhranie zrozumiteľné?</h4>
                    <input type="hidden" id="feedback" name="feedback">
                    <div class="flex">
                        <div class="w-1/3 tCenter" style="cursor: pointer"
                             @click="submitFeedback('bad')">
                            <img src="/images/sad.png" style="width: 150px;" class="mx-auto">
                            <h4>Nie, nevedel/a som to nastaviť</h4>
                        </div>
                        <div class="w-1/3 tCenter" style="cursor: pointer"
                             @click="submitFeedback('neutral')">
                            <img src="/images/neutral.png" style="width: 150px;" class="mx-auto">
                            <h4>Šlo to, ale musel som sa zamýšľať</h4>
                        </div>
                        <div class="w-1/3 tCenter" style="cursor: pointer"
                             @click="submitFeedback('good')">
                            <img src="/images/good.png" style="width: 150px;" class="mx-auto">
                            <h4>Jasné, v pohode</h4>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <h1 class="h1">Ďakujem za Vašu spätnú väzbu.</h1>
                    <p>Veľmi si ju vážim.</p>
                    <p>Ivan</p>
                    <a href="/" class="mx-auto block text-center w-full my-6">
                        <button class="bg-red-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
                            Pokračovať na oznamy
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import CityzenAddress from './address.vue';
    import Categories from './categories.vue';
    import CustomerMap from './customer-map.vue';
    import CustomerEmails from './emails.vue';

    export default {
        components: {
            CityzenAddress,
            Categories,
            CustomerMap,
            CustomerEmails
        },
        data() {
            return {
                cityzen: cityzen,
                city: city,
                step: cityzen.step ? cityzen.step : 'address',
                progress: null,
                feedback: false,
            }
        },
        mounted() {
            this.setProgress();
        },
        methods: {
            submitFeedback(f) {
                ajaxRequest('SaveFeedback', {
                    feedback: f
                }).done(cityzen => {
                    this.feedback = true;
                });
            },
            reset() {
                ajaxRequest('ResetCustomerSettings', {cityzen_id: cityzen.id}).done(() => {
                    window.location.reload();
                });
            },
            savePartial(method, payload) {
                ajaxRequest(method, payload).done(cityzen => {
                    this.cityzen = cityzen;
                    this.step = cityzen.step;
                    this.setProgress();
                });
            },
            setProgress() {
                document.getElementById('customer-progress').innerHTML = '<div class="progress-bar-wrapper my-4"></div>';
                let label = 'Adresa';
                switch (this.step) {
                    case 'address':
                        label = 'Adresa';
                        break;
                    case 'categories':
                        label = 'Kategórie';
                        break;
                    case 'map':
                        label = 'Mapa';
                        break;
                    case 'emails':
                        label = 'Súhrnné zasielanie';
                        break;
                    default:
                        label = 'Súhrnné zasielanie';
                        break;
                }

                let steps = [];

                steps.push("Adresa");
                steps.push("Kategórie");
                steps.push("Mapa");
                steps.push("Súhrnné zasielanie");

                console.log('setProgress', label);
                ProgressBar.singleStepAnimation = 0;
                this.progress = ProgressBar.init(
                    steps,
                    label,
                    'progress-bar-wrapper' // created this optional parameter for container name (otherwise default container created)
                );
                console.log('progress', this.progress);
            }
        }
    }
</script>
