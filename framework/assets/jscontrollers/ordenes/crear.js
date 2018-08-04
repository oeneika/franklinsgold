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
 * Crea la orden
*/
function createOrden(formulario){
    var $ocrendForm = $(this), __data = {};
    $('#'+formulario).serializeArray().map(function(x){__data[x.name] = x.value;}); 

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        $.ajax({
            type : "POST",
            url : "api/orden/crear",
            dataType: 'json',
            data : __data,
            beforeSend: function(){ 
                $ocrendForm.data('locked', true) 
            },
            success : function(json) {
                if(json.success == 1) {
                    toastr.options = {
                        closeButton: true,
                        progressBar: true,
                        showMethod: 'slideDown',
                        timeOut: 4000
                    };
    
                    toastr.info(json.message,'Exito!');
                    
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(json.message, '¡Ups!');
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
 * Trae las monedas segun la sucursal
 */
function getMonedas() {

   $('#id_id_moneda').empty();
    var sucursal = $('#id_id_sucursal').val();

    $.ajax({
        type : "GET",
        url : "api/get/monedas/BySucursal/"+sucursal.toString(),
        success : function(json) {
         
          for (var i = 0; i< json.length; i++){

                $('.selector_moneda').append(new Option(
                    json[i].codigo + " Peso : " + json[i].peso,
                    json[i].codigo,
                    false,
                    true
                ));

          }
        },
        error : function(xhr, status) {
         // error_toastr('Error', 'Ha ocurrido un problema');
        }
    });
   

}



/**
 * Events
 */
$('#crearOrdenCompra').click(function(e) {
    e.defaultPrevented;
    createOrden('crearOrdenCompra_form');
});
$('#crearOrdenVenta').click(function(e) {
    e.defaultPrevented;
    createOrden('crearOrdenVenta_form');
});
$('#crearOrdenIntercambio').click(function(e) {
    e.defaultPrevented;
    createOrden('crearOrdenIntercambio_form');
});

$('form#crearOrdenCompra_form input').keypress(function(e) {
    e.defaultPrevented;
    if(e.which == 13) {
        createOrden('crearOrdenCompra_form');
        return false;
    }
});
$('form#crearOrdenVenta_form input').keypress(function(e) {
    e.defaultPrevented;
    if(e.which == 13) {
        createOrden('crearOrdenVenta_form');
        return false;
    }
});
$('form#crearOrdenIntercambio_form input').keypress(function(e) {
    e.defaultPrevented;
    if(e.which == 13) {
        createOrden('crearOrdenIntercambio_form');
        return false;
    }
});

