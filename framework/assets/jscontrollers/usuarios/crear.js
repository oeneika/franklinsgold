
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
    $('#crearUsuariobtn').attr('disabled','disabled');
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

                toastr.success('¡Usuario creado!','Exito!');
                
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
        complete: function(){ 
            $('#crearUsuariobtn').removeAttr('disabled');
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
$('form#crear_usuario_form').keypress(function (e) {
    e.defaultPrevented;
    if (e.which == 13) {
        createUsuario();
        return false;
    }
});
