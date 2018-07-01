/**
 * Animación de carga en el ícono de edición.
 * Abre el modal para la edición de un usuario.
 * Solicita información de dicho usuario y la despliega.
 * 
 * @param {*} id_usuario
 * @param {*} nombre
 */
function editar_un_usuario(id_usuario, nombre, apellido, email, direccion, telefono, genero, saldo_favor) {/*nombre ,apellido , email ,direecion ,telefono */
    edit_btn_load('#edit_btn_table_' + id_usuario, 1);

    $('#id_edit_usuario').val(id_usuario);
    $('#id_edit_nombre_usuario_0CR3ND').val(nombre);
    $('#id_edit_apellido_usuario_0CR3ND').val(apellido);
    $('#id_edit_saldo_a_favor_usuario_0CR3ND').val(saldo_favor);
    $('#id_edit_email_usuario_0CR3ND').val(email);
    $('#id_edit_direccion_usuario_0CR3ND').val(direccion);
    $('#id_edit_telefono_usuario_0CR3ND').val(telefono);
    $('#editar_usuario_modal_0CR3ND').modal('show');

    if (genero == 'M') {
        $("#id_edit_genero_hombre_usuario_0CR3ND").prop("checked", true);
    } else {
        $("#id_edit_genero_mujer_usuario_0CR3ND").prop("checked", true);
    }

    edit_btn_load('#edit_btn_table_' + id_usuario, 0);
}

/**
 * Ajax action to api rest
*/
function edit_usuario() {
    var l = Ladda.create(document.querySelector('#editar_usuario_0CR3ND'));
    l.start();

    $.ajax({
        type: "POST",
        url: "api/usuarios/editar",
        data: $('#editar_usuario_form_0CR3ND').serialize(),
        success: function (json) {
            if (json.success == 1) {
                success_toastr('Realizado!', json.message);
                setTimeout(function () {
                    location.reload();
                }, 1000);
            } else {
                error_toastr('Ups!', json.message);
            }
        },
        error: function (xhr, status) {
            error_toastr('Error', 'Ha ocurrido un problema');
        },
        complete: function () {
            l.stop();
        }
    });
}

/**
   * Events
   *  
   * @param {*} e 
*/
$('#editar_usuario_0CR3ND').click(function (e) {
    e.defaultPrevented;
    edit_usuario();
});
$('form#editar_usuario_form_0CR3ND input').keypress(function (e) {
    e.defaultPrevented;
    if (e.which == 13) {
        edit_usuario();

        return false;
    }
});