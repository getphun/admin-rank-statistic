$(function(){
    var source = $('#data-chart');
    if(!source.get(0))
        return;
    
    var chartData,
        data = JSON.parse(source.text()),
        ctx  = $('#rank-chart');
    
    ctx.attr({
        'width': ctx.parent().width(),
        'height': ctx.parent().width() * 9 / 16
    });
    
    chartData = {
        labels: [],
        datasets: [
            {
                label: 'International',
                yAxisID: "y-axis-international",
                fill: false,
                backgroundColor: "#428bca",
                borderColor: "rgba(66,139,202,1)",
                data: []
            },
            {
                label: 'Local',
                yAxisID: "y-axis-local",
                fill: false,
                backgroundColor: "#d9534f",
                borderColor: "rgba(217,83,79,1)",
                data: []
            }
        ]
    };
    
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    for(var i=0; i<data.length; i++){
        var row = data[i];
        
        // labels
        var crts = row.created.split(' ')[0].split('-');
        var lab  = months[parseInt(crts[1])] + ' ' + crts[2];
        chartData.labels.push(lab);
        
        // international
        chartData.datasets[0].data.push( { x: lab, y: row.international } );
        chartData.datasets[1].data.push( { x: lab, y: row.local } );
    }
    
    new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            animation: false,
            responsive: true,
            scales: {
                yAxes: [
                    {
                        position: 'left',
                        id: 'y-axis-international',
                        ticks: {
                            reverse: true
                        }
                    },
                    {
                        position: 'right',
                        id: 'y-axis-local',
                        ticks: {
                            reverse: true
                        }
                    }
              ]
            }
        }
    });
});