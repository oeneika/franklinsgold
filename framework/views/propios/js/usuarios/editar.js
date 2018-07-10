/**
 * 
 * @param {*} id_user 
 * @param {*} tipo 
 * @param {*} primer_nombre 
 * @param {*} segundo_nombre 
 * @param {*} primer_apellido 
 * @param {*} segundo_apellido 
 * @param {*} usuario 
 * @param {*} sexo 
 * @param {*} telefono 
 * @param {*} email 
 */
function editar_un_usuario(id_user,tipo,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido,sexo,telefono) {

    $('#id_id_user').val(id_user);
    $('#id_primer_nombre').val(primer_nombre);
    $('#id_segundo_nombre').val(segundo_nombre);
    $('#id_primer_apellido').val(primer_apellido);
    $('#id_segundo_apellido').val(segundo_apellido);
    $('#id_telefono').val(telefono);
    

    if (sexo == 'm') {
        $("#id_sexom").prop("checked", true);
    } else {
        $("#id_sexof").prop("checked", true);
    }

    if (tipo == 0) {
        $("#id_tipoa").prop("checked", true);
    } else {
        $("#id_tipov").prop("checked", true);
    }
    $('#editarUsuario').modal('show');

}

/**
 * Ajax action to api rest
*/
function edit_usuario() {
    /*var l = Ladda.create(document.querySelector('#editar_usuario_0CR3ND'));
    l.start();*/

    $.ajax({
        type: "POST",
        url: "api/usuarios/editar",
        data: $('#editar_usuario_form').serialize(),
        success: function (json) {
            if(json.success == 1) {

                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    showMethod: 'slideDown',
                    timeOut: 4000
                };

                toastr.success('¡Usuario editado!','Exito!');
                
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
          //  l.stop();
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