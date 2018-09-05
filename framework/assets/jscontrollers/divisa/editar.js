/**
 * 
 * @param {int} id_divisa : contiene el id de la divisa en la db
 * @param {string} nombre : nombre de la divisa
 * @param {int} precio_dolares : precio de compra de la divisa
 * @param {int} precio_dolares_venta : precio de venta de la divisa
 */
function editar_una_divisa(id_divisa,nombre,precio_dolares,precio_dolares_venta) {

    $('#id_id_divisa').val(id_divisa);
    $('#id_nombre').val(nombre);
    $('#id_precio_dolares').val(precio_dolares);
    $('#id_precio_dolares_venta').val(precio_dolares_venta);

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

                toastr.success(json.message,'¡Éxito!');
                
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