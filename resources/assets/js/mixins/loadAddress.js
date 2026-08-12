export default {
    data() {
        return {
            numbers: [],
            streets: streets,
            numberSelectize: null,
        }
    },
    mounted() {
        vueEventBus.$on('loadStreetNumbersSelectize', street => {
            this.street = street;
            this.loadStreetNumbers();
        });
        $('#street').selectize({
            create: false,
            sortField: 'text',
        });
    },
    methods: {
        loadStreetNumbers() {
            console.log('loadStreetNumbers');
            ajaxRequest('LoadStreetNumbers', {
                street: this.street
            }).done(numbers => {
                this.numbers = numbers;
                let ids = this.numbers.map(n => n.id);
                console.log('ids', ids);
                setTimeout(() => {
                    if (this.numberSelectize) {
                        console.log('this.numberSelectize', this.numberSelectize);
                        this.numberSelectize[0].selectize.clearOptions();
                        this.numberSelectize[0].selectize.refreshOptions();
                    } else {
                        // this.numberSelectize = $('#number').selectize({
                        //     create: false,
                        // });
                    }
                }, 1000);
                if (ids.indexOf(this.number) === -1) {
                    console.log('removing number', this.number);
                    this.number = '';
                }

            });
        }
    }
}