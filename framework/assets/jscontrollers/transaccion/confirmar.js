/**
 * Abre modal de confirmacion
 */
function confirmarTransaccion(){
    $('#confirmarTransacciones').modal('show');
}

function ajaxConfirm(){
    $.ajax({
        type: "POST",
        url: "api/transaccion/qr/concretar",
        data: $('#confirmar_transacciones_form').serialize(),
        success: function (json) {

            if(json.success == 1) {

                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    showMethod: 'slideDown',
                    timeOut: 4000
                };

                toastr.success(json.message,'¡Éxito!');
                
                setTimeout(function () {
                    location.reload();
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
          $('#confirmartransaccionesbtn').removeAttr('disabled');
        }
    });
}

/**
 * Ajax action to api rest
*/
function confirmar() {
    $('#confirmartransaccionesbtn').attr('disabled','disabled');
    $.ajax({
        type: "GET",
        url: "api/get/transaccion_en_espera/" + $('#codigo_confirmacion').val(),
        success: function (json){
            if(json == false){
                toastr.error("Codigo invalido", '¡Ups!');
                $('#confirmartransaccionesbtn').removeAttr('disabled');
            }
            else{
                $('#id_usuario_con').val(json[0].email);
                $('#id_codigo_con').val(json[0].codigo_qr_moneda);
                ajaxConfirm();
            }
            
        },
        error: function(xhr, status){
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
        }
    });
}


/**
 * Events
 *  
 * @param {*} e 
 */
$('#confirmartransaccionesbtn').click(function (e) {
    e.defaultPrevented;
    confirmar();
});
$('confirmar_transacciones_form').keypress(function (e) {
    e.defaultPrevented;
    if (e.which == 13) {
        confirmar();
        return false;
    }
});