<style>
    #pie{
        margin-top: 5%;
        margin-bottom: 8%;
        margin-left: 25%;
        -webkit-box-shadow: 5px 5px 20px -10px rgba(100,100,100,0.62);
        box-shadow: 5px 5px 20px -10px rgba(100,100,100,0.62);

        max-width: 50%;
        height: auto;
    }
</style>
    <div id="pie">
        <figure class="highcharts-figure">
            <div id="container"></div>
            <p class="highcharts-description">
            </p>
        </figure>
    </div>


<script>
    let types = {!! json_encode($types) !!}
        let
    createdByMonth = {!! json_encode($createdByMonth) !!}
        let
    categories = {!! json_encode($categories) !!}
    console.log('dataZoServ2', createdByMonth);
    let seriesPieChart = [];
    let medzivysledky = {};
    //kolacovy graf


    for (let type in types) {
        let oneEvidence = types[type];
        console.log(oneEvidence.count);
        seriesPieChart.push(
            {
                name: oneEvidence.name,
                y: parseInt(oneEvidence.count)
            }
        );
    }

    console.log('seriesPieChart', seriesPieChart);
    Highcharts.chart('container', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Štatistiky oznamov'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
            name: 'Počet % z celkového počtu oznamov',
            data: seriesPieChart
        }]
    });
</script>

