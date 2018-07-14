/**
 * Abre el modal para un nuevo sucursal
 */
function crearafiliado() {
    numTelefonos = 1;
    $("#telefonos").empty()
    $('#crearAfiliado').modal('show');
}

/**
 * Ajax action to api rest
*/
function crear_afiliado(){
    var $ocrendForm = $(this), __data = {};
    $('#crear_afiliado_form').serializeArray().map(function(x){__data[x.name] = x.value;}); 

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        $('#crearafiliadobtn').attr('disabled','disabled');
        $.ajax({
            type : "POST",
            url : "api/afiliados/crear",
            dataType: 'json',
            data : __data,
            beforeSend: function(){ 
                $ocrendForm.data('locked', true) 
            },
            success : function(json) {
                if(json.success == 1) {
                    setTimeout(function(){
                        toastr.success(json.message);
                        location.href='afiliados/afiliados/';
                    },1000);
                } else {
                    toastr.error(json.message);
                }
            },
            error : function(xhr, status) {
                toastr.error('Ha ocurrido un problema interno');
            },
            complete: function(){ 
                $('#crearafiliadobtn').removeAttr('disabled');
                $ocrendForm.data('locked', false);
            } 
        });
    }
} 

/**
 * Events
 */
$('#crearafiliadobtn').click(function(e) {
    e.defaultPrevented;
    crear_afiliado();
});
$('form#crear_afiliado_form input').keypress(function(e) {
    e.defaultPrevented;
    if(e.which == 13) {
        crear_afiliado();

        return false;
    }
});
