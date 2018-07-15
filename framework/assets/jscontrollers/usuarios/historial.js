
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
                    <td>${json[i].nombre == null ? "" : json[i].nombre}</td>
                </tr>`                
                );           
          }//fecha('D, d F, Y h:i a', json[i].fecha)   date('F j, Y',json[i].fecha)  

        },
        error : function(xhr, status) {
            toastr.error('Ha ocurrido un problema', '¡Ups!');
        }
    });

    $('#historialUsuario').modal('show');
}
