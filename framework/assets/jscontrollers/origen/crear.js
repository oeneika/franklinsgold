/**
 * Abre el modal para un nuevo Origen
 */
function crearOrigen() {
    $('#crearOrigen').modal('show');
}

/**
 * Ajax action to api rest
*/

function createOrigen() {
    $('#crearOrigenbtn').attr('disabled','disabled');
    $.ajax({
        type: "POST",
        url: "api/origen/crear",
        data: $('#crear_origen_form').serialize(),
        success: function (json) {

            if(json.success == 1) {

                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    showMethod: 'slideDown',
                    timeOut: 4000
                };

                toastr.info('¡Origen creado!','Exito!');
                
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
        complete: function(){ 
            $('#crearOrigenbtn').removeAttr('disabled');
        } 
    });
}


/**
 * Events
 *  
 * @param {*} e 
 */
$('#crearOrigenbtn').click(function (e) {
    e.defaultPrevented;
    createOrigen();
});
$('crear_origen_form').keypress(function (e) {
    e.defaultPrevented;
    if (e.which == 13) {
        createOrigen();
        return false;
    }
});
