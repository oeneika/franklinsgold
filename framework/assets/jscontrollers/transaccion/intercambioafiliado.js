function crearTransaccion() {
    $('#crearintercambioAfiliado').modal('show');
}

function showIntercambios(id_user,id_comercio){
    $('#body_intercambios').empty();
    $('#crearnuevointercambiobtn').attr('onclick',`crearintercambio(${id_user})`);
    $.ajax({
        type : "GET",
        url : `api/afiliados/getIntercambiosUser/${id_user}/${id_comercio}`,
        success : function(json) {
            console.log(json);
            for (let i = 0; i < json['intercambios'].length; i++) {
                $('#body_intercambios').append(
                `<tr>
                    <td>${json['intercambios'][i].codigo}</td>
                    <td>${date('F j, Y',json['intercambios'][i].fecha)}</td>
                    <td>$${number_format(json['intercambios'][i].monto,2,'.',',')}</td>
                </tr>`)  
            }
            $("#total_intercambio").html(`$${number_format(json['total'][0].total,2,'.',',')}`);
        },
        error : function(xhr, status) {
            toastr.error('Ha ocurrido un problema interno');
        },
        complete: function(){ 
            $("#intercambioAfiliados").modal('show');
        } 
    });
}
    

/**
 * Ajax action to api rest
*/
function createTransaccion() {
    $('#creartransaccionesbtn').attr('disabled','disabled');
    $.ajax({
        type: "POST",
        url: "api/transaccion/intercambioAfiliado",
        data: $('#crear_transacciones_form').serialize(),
        success: function (json) {

            if(json.success == 1) {

                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    showMethod: 'slideDown',
                    timeOut: 4000
                };

                toastr.success('¡Transacción creada!','Exito!');
                
                setTimeout(function () {
                    location.reload(true);
                }, 1000);
                
            }else {
                toastr.error(json.message, '¡Ups!');
            }

        },
        error: function (xhr, status) {

            toastr.error("Ha ocurrido un problema", '¡ERROR!');

            toastr.options = {
                "closeButton": false,
                "debug": false,
                "progressBar": false,
                "preventDuplicates": false,
                "positionClass": "toast-top-right",
                "onclick": null,
                "showDuration": "400",
                "hideDuration": "1000",
                "timeOut": "3000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }
        },
        complete: function () {
          $('#creartransaccionesbtn').removeAttr('disabled');
        }
    });
}


/**
 * Events
 *  
 * @param {*} e 
 */
$('#creartransaccionesbtn').click(function (e) {
    e.defaultPrevented;
    createTransaccion();
});
$('crear_transacciones_form').keypress(function (e) {
    e.defaultPrevented;
    if (e.which == 13) {
        createTransaccion();
        return false;
    }
});