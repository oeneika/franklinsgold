/**
 * Abre el modal para un nuevo sucursal
 */
function editarSucursal(id,nombre,direccion) {
    $('#id_edit_sucursal').val(id);
    $('#id_edit_nombre').val(nombre);
    $('#id_edit_direccion').val(direccion);
    $('#editarSucursal').modal('show');
}

/**
 * Ajax action to api rest
*/
function editar_sucursal(){
    var $ocrendForm = $(this), __data = {};
    $('#editar_sucursal_form').serializeArray().map(function(x){__data[x.name] = x.value;}); 

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        console.log(__data);
        $.ajax({
            type : "POST",
            url : "api/sucursal/editar",
            dataType: 'json',
            data : __data,
            beforeSend: function(){ 
                $ocrendForm.data('locked', true) 
            },
            success : function(json) {
                if(json.success == 1) {
                    setTimeout(function(){
                        toastr.success(json.message);
                        location.href='sucursal/sucursal/';
                    },1000);
                } else {
                    toastr.error(json.message);
                }
            },
            error : function(xhr, status) {
                toastr.error('Ha ocurrido un problema interno');
            },
            complete: function(){ 
                $ocrendForm.data('locked', false);
            } 
        });
    }
} 

/**
 * Events
 */
$('#editarsucursalbtn').click(function(e) {
    e.defaultPrevented;
    editar_sucursal();
});
$('form#editar_sucursal_form input').keypress(function(e) {
    e.defaultPrevented;
    if(e.which == 13) {
        editar_sucursal();

        return false;
    }
});
