/**
 * 
 * @param {*} id_divisa 
 * @param {*} nombre 
 */
function editar_una_divisa(id_divisa,nombre,precio_dolares) {

    $('#id_id_divisa').val(id_divisa);
    $('#id_nombre').val(nombre);
    $('#id_precio_dolares').val(precio_dolares);


    $('#editardivisa').modal('show');

}

/**
 * Ajax action to api rest
*/
function edit_divisa() {
    /*var l = Ladda.create(document.querySelector('#editar_usuario_0CR3ND'));
    l.start();*/

    $('#editardivisabtn').attr('disabled','disabled');
    $.ajax({
        type: "POST",
        url: "api/divisa/editar",
        data: $('#editar_divisa_form').serialize(),
        success: function (json) {
            if(json.success == 1) {

                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    showMethod: 'slideDown',
                    timeOut: 4000
                };

                toastr.success(json.message,'Exito!');
                
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
            $('#editardivisabtn').removeAttr('disabled');
        }
    });
}

/**
   * Events
   *  
   * @param {*} e 
*/
$('#editardivisabtn').click(function (e) {
    e.defaultPrevented;
    edit_divisa();
});
$('form#editar_divisa_form input').keypress(function (e) {
    e.defaultPrevented;
    if (e.which == 13) {
        edit_divisa();

        return false;
    }
});