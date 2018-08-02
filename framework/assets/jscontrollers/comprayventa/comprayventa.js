/**
 * Abre el modal para realizar una compra
 */
function intercambioModal() {
    $('#intercambioModal').modal('show');
}
/**
 * Abre el modal para realizar una compra
 */
function compraModal() {
    $('#compraModal').modal('show');
}
/**
 * Abre el modal para realizar una venta
 */
function ventaModal() {
    $('#ventaModal').modal('show');
}
/**
 * Ajax action to api rest
*/
function comprayventa(){
    var $ocrendForm = $(this), __data = {};
    $('#comprayventa_form').serializeArray().map(function(x){__data[x.name] = x.value;}); 

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        $.ajax({
            type : "POST",
            url : "api/comprayventa",
            dataType: 'json',
            data : __data,
            beforeSend: function(){ 
                $ocrendForm.data('locked', true) 
            },
            success : function(json) {
                if(json.success == 1) {
                    alert(json.message);
                } else {
                    alert(json.message);
                }
            },
            error : function(xhr, status) {
                alert('Ha ocurrido un problema interno');
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
$('#comprayventa').click(function(e) {
    e.defaultPrevented;
    comprayventa();
});
$('form#comprayventa_form input').keypress(function(e) {
    e.defaultPrevented;
    if(e.which == 13) {
        comprayventa();

        return false;
    }
});
