/**
 * 
 * @param {*} id_origen 
 * @param {*} nombre 
 */
function editar_un_origen(id_origen,nombre,abreviatura) {

    $('#id_id_origen').val(id_origen);
    $('#id_nombre').val(nombre);
    $('#id_abreviatura').val(abreviatura);


    $('#editarorigen').modal('show');

}

/**
 * Ajax action to api rest
*/
function edit_origen() {
    /*var l = Ladda.create(document.querySelector('#editar_usuario_0CR3ND'));
    l.start();*/

    $('#editarorigenbtn').attr('disabled','disabled');
    $.ajax({
        type: "POST",
        url: "api/origen/editar",
        data: $('#editar_origen_form').serialize(),
        success: function (json) {
            if(json.success == 1) {

                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    showMethod: 'slideDown',
                    timeOut: 4000
                };

                toastr.info(json.message,'¡Éxito!');
                
                setTimeout(function () {
                    location.reload();
                }, 1000);
                
            }else {
                toastr.error(json.message, '¡Ups!')
            }
        },
        error: function (xhr, status) {
            toastr.error("Ha ocurrido un problema", '¡ERROR!');
        },
        complete: function () {
            $('#editarorigenbtn').removeAttr('disabled');
        }
    });
}

/**
   * Events
   *  
   * @param {*} e 
*/
$('#editarorigenbtn').click(function (e) {
    e.defaultPrevented;
    edit_origen();
});
$('form#editar_origen_form input').keypress(function (e) {
    e.defaultPrevented;
    if (e.which == 13) {
        edit_origen();

        return false;
    }
});