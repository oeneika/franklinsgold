/**
 * Abre el modal para un nuevo sucursal
 */
function crearsucursal() {
    $('#crearSucursal').modal('show');
}

/**
 * Ajax action to api rest
*/

function crearsucursalForm() {
    $.ajax({
        type: "POST",
        url: "api/sucursals/crear",
        data: $('#crear_sucursal_form').serialize(),
        success: function (json) {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                showMethod: 'slideDown',
                timeOut: 4000
            };
            toastr.info('Franklins Gold', '¡Bienvenido al sistema!');
            if (json.success == 1) {
                setTimeout(function () {
                    location.reload();
                }, 1000);
            }
        },
        error: function (xhr, status) {
            toastr[error]("El sucursal no se pudo registrar en el sistema.", "¡ERROR!")

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
$('#crearsucursalbtn').click(function (e) {
    e.defaultPrevented;
    crearsucursalForm();
});
$('crear_sucursal_form').keypress(function (e) {
    e.defaultPrevented;
    if (e.which == 13) {
        crearsucursalForm();
        return false;
    }
});
