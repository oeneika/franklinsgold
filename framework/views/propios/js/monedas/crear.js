/**
 * Abre el modal para una nueva moneda
 */
function crearMoneda() {
    $('#crearMoneda').modal('show');
}

function createMoneda() {
    $.ajax({
        type: "POST",
        url: "api/monedas/crear",
        data: $('#crear_moneda_form').serialize(),
        success: function (json) {

            if(json.success == 1) {

                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    showMethod: 'slideDown',
                    timeOut: 4000
                };

                toastr.info('¡Moneda creada!','Exito!');
                
                setTimeout(function () {
                    location.reload();
                }, 1000);
                
            }else {
                toastr.info(json.message,'Ups!');
               //error_toastr('Ups!', json.message);
            }

        },
        error: function (xhr, status) {
            //toastr[error]("El usuario no se pudo registrar en el sistema.", "¡ERROR!");
            toastr.info('Error','Ha ocurrido un problema!');
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
$('#crearMonedabtn').click(function (e) {
    e.defaultPrevented;
    createMoneda();
});
$('crear_moneda_form').keypress(function (e) {
    e.defaultPrevented;
    if (e.which == 13) {
        createMoneda();
        return false;
    }
});
