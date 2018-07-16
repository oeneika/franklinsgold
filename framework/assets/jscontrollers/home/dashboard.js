$(document).ready(function() {
    var lineData;
    $.ajax({
        type : "GET",
        url : "https://www.quandl.com/api/v3/datasets/LBMA/SILVER.json?api_key=CPE8TFT3Z18GjsP3C9pV", //Json con el precio de la plata
        dataType: 'json',
        success : function(json) {
            var label=[], data=[];
            var dias = 10;
            
            //Se obtienen los datos necesarios de los ultimos dias definidos
            for(var i=0; i<= dias; i++){
                label[i]=json.dataset.data[dias-i][0];
                data[i]=json.dataset.data[dias-i][1].toFixed(2);
            }
            
            lineData = {
                labels: label,
                datasets: [
                    {
                        label: "Plata",
                        fillColor: "rgba(221, 175, 5,0.5)",
                        strokeColor: "rgba(221, 175, 5,1)",
                        pointColor: "rgba(221, 175, 5,1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(221, 175, 5,1)",
                        data: data
                    }
                ]
            };

            var lineOptions = {
                scaleShowGridLines: true,
                scaleGridLineColor: "rgba(0,0,0,.05)",
                scaleGridLineWidth: 1,
                bezierCurve: true,
                bezierCurveTension: 0.4,
                pointDot: true,
                pointDotRadius: 4,
                pointDotStrokeWidth: 1,
                pointHitDetectionRadius: 20,
                datasetStroke: true,
                datasetStrokeWidth: 2,
                datasetFill: true,
                responsive: true,
            };


            var ctx = document.getElementById("plataChart").getContext("2d");
            var myNewChart = new Chart(ctx).Line(lineData, lineOptions);
        },
        error : function(xhr, status) {
            toastr.error('Ha ocurrido un problema cargando la grafica de plata');
        },
        complete: function(){ 
        } 
    });

    $(document).ready(function() {
        var lineData
        $.ajax({
            type : "GET",
            url : "https://www.quandl.com/api/v3/datasets/LBMA/GOLD.json?api_key=CPE8TFT3Z18GjsP3C9pV",
            dataType: 'json',
            success : function(json) {
                var label=[], data=[];
                var dias = 10;
                
                for(var i=0; i<= dias; i++){
                    label[i]=json.dataset.data[dias-i][0];
                    data[i]=json.dataset.data[dias-i][1];
                }
                
                lineData = {
                    labels: label,
                    datasets: [
                        {
                            label: "Oro",
                            fillColor: "rgba(221, 175, 5,0.5)",
                            strokeColor: "rgba(221, 175, 5,1)",
                            pointColor: "rgba(221, 175, 5,1)",
                            pointStrokeColor: "#fff",
                            pointHighlightFill: "#fff",
                            pointHighlightStroke: "rgba(221, 175, 5,1)",
                            data: data
                        }
                    ]
                };

                var lineOptions = {
                    scaleShowGridLines: true,
                    scaleGridLineColor: "rgba(0,0,0,.05)",
                    scaleGridLineWidth: 1,
                    bezierCurve: true,
                    bezierCurveTension: 0.4,
                    pointDot: true,
                    pointDotRadius: 4,
                    pointDotStrokeWidth: 1,
                    pointHitDetectionRadius: 20,
                    datasetStroke: true,
                    datasetStrokeWidth: 2,
                    datasetFill: true,
                    responsive: true,
                };
    
    
                var ctx = document.getElementById("lineChart").getContext("2d");
                var myNewChart = new Chart(ctx).Line(lineData, lineOptions);
            },
            error : function(xhr, status) {
                toastr.error('Ha ocurrido un problema cargando la grafica de oro');
            },
            complete: function(){ 
            } 
        });

    });

});