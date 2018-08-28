//Incluye un script
$.ajax({
    url: 'views/propios/js/php.js',
    dataType: 'script',
    async: false
  });

/**
 * Abre el modal para el historial de usuario
 */
function historialUsuario(id) {
    $('#body_historial').empty();
    $('#body_historial2').empty();

    $.ajax({
        type : "GET",
        url : "api/transaccion/get/"+id.toString(),
        success : function(json) {
         
          for (var i = 0; i< json.length; i++){

            var mitipo = "";
            if(json[i].tipo == 1){
                mitipo = "Compra";
            }else
            if(json[i].tipo == 2){
                mitipo = "Venta";
            }else
            if(json[i].tipo == 3){
                mitipo = "Intercambio";
            }


                $('#body_historial').append(
                `<tr>
                    <td>${mitipo}</td>
                    <td>${json[i].id_usuario == id ? json[i].m1 : json[i].m2}</td>
                    <td>${date('F j, Y',json[i].fecha)}</td>
                    
                </tr>`                
                );           
          }//<td>${json[i].nombre == null ? "" : json[i].nombre}</td> 

        },
        error : function(xhr, status) {
            toastr.error('Ha ocurrido un problema', '¡Ups!');
        }
    });


    $.ajax({
        type : "GET",
        url : "api/orden/get/"+id.toString(),
        success : function(json) {
         
          for (var i = 0; i< json.length; i++){

            var tipo = "";
            if(json[i].tipo_orden == 1){
                tipo = "Compra";
            }else
            if(json[i].tipo_orden == 2){
                tipo = "Venta";
            }


            $('#body_historial2').append(
            `<tr>
                <td>${tipo}</td>
                <td>${json[i].tipo_gramo}</td>
                <td>${json[i].cantidad}</td>
                <td>${ number_format(json[i].precio,2,",",".")}</td>
                <td>${date('F j, Y',json[i].fecha)}</td>          
            </tr>`                
            );    

          }

        },
        error : function(xhr, status) {
            toastr.error('Ha ocurrido un problema', '¡Ups!');
        }
    });

    $('#historialUsuario').modal('show');
}
