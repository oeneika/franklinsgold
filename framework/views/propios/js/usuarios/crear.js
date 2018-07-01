/**
 * Abre el modal para un nuevo usuario
 */
function crearUsuario() {
    $('#crearUsuario').modal('show');
}

/**
 * Ajax action to api rest
*/

function crearUsuarioForm() {
    $.ajax({
        type: "POST",
        url: "api/usuarios/crear",
        data: $('#crear_usuario_form').serialize(),
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
            toastr[error]("El usuario no se pudo registrar en el sistema.", "¡ERROR!")

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
$('#crearUsuariobtn').click(function (e) {
    e.defaultPrevented;
    crearUsuarioForm();
});
$('crear_usuario_form').keypress(function (e) {
    e.defaultPrevented;
    if (e.which == 13) {
        crearUsuarioForm();
        return false;
    }
});
