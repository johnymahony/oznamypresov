
    <style>
        #line{
            margin-top: 5%;
            margin-bottom: 8%;
            margin-left: 25%;
            -webkit-box-shadow: 5px 5px 20px -10px rgba(100,100,100,0.62);
            box-shadow: 5px 5px 20px -10px rgba(100,100,100,0.62);

            max-width: 50%;
            height: auto;
        }


    </style>
<div id="line">
    <figure class="highcharts-figure">
        <div id="container2"></div>
        <p class="highcharts-description">
        </p>
    </figure>
</div>

<script>
    let seriesLineChart = [];
    let caption, value
    let data = [];
    let months = [];

    for (let dataMonth in createdByMonth) {
        let oneData = createdByMonth[dataMonth];
        console.log(oneData);
        data.push(parseInt(oneData.count));
        months.push(oneData.month);
        console.log('seriesLinechart', seriesLineChart);
    }
    seriesLineChart.push({
            name: 'Počet v jednom mesiaci',
            data: data
        }
    );

    Highcharts.chart('container2', {
        title: {
            text: 'Mesačný prehľad'
        },
        subtitle: {
            text: ''
        },
        yAxis: {
            title: {
                text: 'Počet údajov za mesiac'
            }
        },
        xAxis: {
            accessibility: {
                rangeDescription: 'Range:2021 '
            },
            categories: months,

        },

        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle'
        },

        plotOptions: {
            series: {
                label: {
                    connectorAllowed: false
                }
            }
        },
        series: seriesLineChart,


        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                },
                chartOptions: {
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                }
            }]
        }

    });
</script>
