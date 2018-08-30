$(document).ready(function(){
    $.ajax({
        type : "POST",
        url : "https://goldiraguide.org/wp-admin/admin-ajax.php", //Json con el precio del oro
        data: {action: "getMetalPrice", api_key: "anonymous"},
        dataType: 'json',
        success : function(json) {
            
            console.log(json);

            //Buscamos la ulitma fecha de actualizacion y la traducimos
            var ultima_fecha = json['last_updated'];
            ultima_fecha = ultima_fecha.replace('Prices last updated on','Actualizado el');
            $('#fecha_oro').html(ultima_fecha);
            $('#fecha_plata').html(ultima_fecha);

            //Datos del oro
            var lineDataOro = {
                labels: json['buttonFrame']['gold']['1m']['labels'],
                datasets: [
                    {
                        label: "Oro",
                        fillColor: "rgba(221, 175, 5,0.5)",
                        strokeColor: "rgba(221, 175, 5,1)",
                        pointColor: "rgba(221, 175, 5,1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(221, 175, 5,1)",
                        data: json['buttonFrame']['gold']['1m']['data']
                    }
                ]
            };

           /* var precios_oro = json['buttonFrame']['gold']['1m']['data'];
            console.log(precios_oro[precios_oro.length-1]);*/

            //Datos de la plata
            var lineDataPlata = {
                labels: json['buttonFrame']['silver']['1m']['labels'],
                datasets: [
                    {
                        label: "Plata",
                        fillColor: "rgba(221, 175, 5,0.5)",
                        strokeColor: "rgba(221, 175, 5,1)",
                        pointColor: "rgba(221, 175, 5,1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(221, 175, 5,1)",
                        data: json['buttonFrame']['silver']['1m']['data']
                    }
                ]
            };

            /*var precios_plata = json['buttonFrame']['silver']['1m']['data'];
            console.log(precios_plata[precios_plata.length-1]);  */     

            var lineOptions = {
                scaleShowGridLines: true,
                scaleGridLineColor: "rgba(0,0,0,.05)",
                scaleGridLineWidth: 1,
                bezierCurve: true,
                bezierCurveTension: 0.4,
                pointDot: true,
                pointDotRadius: 4,
                pointDotStrokeWidth: 1,
                pointHitDetectionRadius: 1,
                datasetStroke: true,
                datasetStrokeWidth: 2,
                datasetFill: true,
                responsive: true,
            };


            //Se obtiene el contexto del DOm
            var ctxOro = document.getElementById("lineChart").getContext("2d");
            var ctxPlata = document.getElementById("plataChart").getContext("2d");

            //Se instancian las graficas
            var oroChart = new Chart(ctxOro).Line(lineDataOro, lineOptions);
            var plataChart = new Chart(ctxPlata).Line(lineDataPlata, lineOptions);
        },
        error : function(xhr, status) {
            toastr.error('Ha ocurrido un problema cargando la grafica de oro');
        },
        complete: function(){ 
        } 
    });
});