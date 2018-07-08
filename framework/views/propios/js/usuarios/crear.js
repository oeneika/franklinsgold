/**
 * Abre el modal para un nuevo usuario
 */
function crearUsuario() {
    $('#crearUsuario').modal('show');
}

/**
 * Ajax action to api rest
*/

function createUsuario() {
    $.ajax({
        type: "POST",
        url: "api/usuarios/crear",
        data: $('#crear_usuario_form').serialize(),
        success: function (json) {

            if(json.success == 1) {

                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    showMethod: 'slideDown',
                    timeOut: 4000
                };

                toastr.info('¡Usuario creado!','Exito!');
                
                setTimeout(function () {
                    location.reload();
                }, 1000);
                
            }else {
                toastr.error('El usuario no pudo crearse, por favor complete todos los campos.', '¡ERROR!')
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
$('#crearUsuariobtn').click(function (e) {
    e.defaultPrevented;
    createUsuario();
});
$('crear_usuario_form').keypress(function (e) {
    e.defaultPrevented;
    if (e.which == 13) {
        createUsuario();
        return false;
    }
});
