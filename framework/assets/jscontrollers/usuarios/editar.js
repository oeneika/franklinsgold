/**
 * 
 * @param {*} id_user 
 * @param {*} tipo 
 * @param {*} primer_nombre 
 * @param {*} segundo_nombre 
 * @param {*} primer_apellido 
 * @param {*} segundo_apellido 
 * @param {*} sexo 
 * @param {*} telefono 
 * @param {*} numero_cuenta 
 * @param {*} id_sucursal 
 * @param {*} id_comercio 
 */
function editar_un_usuario(id_user,tipo,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido,sexo,telefono,numero_cuenta,id_sucursal,id_comercio) {

    $('#id_id_user').val(id_user);
    $('#id_primer_nombre').val(primer_nombre);
    $('#id_segundo_nombre').val(segundo_nombre);
    $('#id_primer_apellido').val(primer_apellido);
    $('#id_segundo_apellido').val(segundo_apellido);
    $('#id_telefono').val(telefono);
    $('#id_numero_cuenta').val(numero_cuenta);
    $('#id_sucursal').val(null).change();
    $('#id_comercio').val(null).change();
    

    if (sexo == 'm') {
        $("#id_sexom").prop("checked", true);
    } else {
        $("#id_sexof").prop("checked", true);
    }

     //Administradores
    if (tipo == 0) {
        $("#id_tipoa").prop("checked", true);
        $('.selects_body').hide();
    } else
    //Vendedores
    if (tipo == 1) {
        $("#id_tipov").prop("checked", true);
        $('.selects_body').show();
        $('#id_sucursal').val(id_sucursal).change();
        $('#id_comercio').val(id_comercio).change();
    } else
    //Clientes
    if (tipo == 2){
        $("#id_tipoc").prop("checked", true);
        $('.selects_body').hide();
    } else
    //Supervisores
    if (tipo == 3){
        $("#id_tipos").prop("checked", true);
        $('.selects_body').hide();
    }
    
    
    $('#editarUsuario').modal('show');

}

/**
 * Ajax action to api rest
*/
function edit_usuario() {
    /*var l = Ladda.create(document.querySelector('#editar_usuario_0CR3ND'));
    l.start();*/
    $('#editar_usuario_0CR3ND').attr('disabled','disabled');
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
            $('#editar_usuario_0CR3ND').removeAttr('disabled');
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