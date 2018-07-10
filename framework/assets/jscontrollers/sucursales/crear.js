/**
 * Abre el modal para un nuevo sucursal
 */
function crearsucursal() {
    $('#crearSucursal').modal('show');
}

/**
 * Ajax action to api rest
*/
function crear_sucursal(){
    var $ocrendForm = $(this), __data = {};
    $('#crear_sucursal_form').serializeArray().map(function(x){__data[x.name] = x.value;}); 

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        $('#crearsucursalbtn').attr('disabled','disabled');
        $.ajax({
            type : "POST",
            url : "api/sucursal/crear",
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
                $('#crearsucursalbtn').removeAttr('disabled');
                $ocrendForm.data('locked', false);
            } 
        });
    }
} 

/**
 * Events
 */
$('#crearsucursalbtn').click(function(e) {
    e.defaultPrevented;
    crear_sucursal();
});
$('form#crear_sucursal_form input').keypress(function(e) {
    e.defaultPrevented;
    if(e.which == 13) {
        crear_sucursal();

        return false;
    }
});
