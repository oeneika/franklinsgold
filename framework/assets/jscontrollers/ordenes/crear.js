/**
 * Variables que contienen los precios actuales del oro y de la plata
 */
var ultimo_precio_oro;
var ultimo_precio_plata;

/**
 * 
 * @param {string} modal : nombre del modal
 * @param {int} ultimo_precio_oro : precio actual del oro
 * @param {int} ultimo_precio_plata : precio actual de la plata
 */
function showModal(modal,ultimo_precio_oro,ultimo_precio_plata) {
    this.ultimo_precio_oro = ultimo_precio_oro;
    this.ultimo_precio_plata = ultimo_precio_plata;
    $('#'+modal).modal('show');
}

/**
 * Resetea el monto de la transacción en un input
 * @param {int} id_cantidad : id del input a sacar la cantidad
 * @param {int} id_monto : id del input a colocar el monto
 * @param {int} id_tipo : id del input que me indica el tipo de gramo
 */
function resetMonto(id_cantidad,id_monto,id_tipo){

    var cantidad = $('#'+id_cantidad).val();

    if($('#'+id_tipo).val() === 'oro'){
        var monto = cantidad * (this.ultimo_precio_oro/28.3495);
    }else
    if($('#'+id_tipo).val() === 'plata'){
        var monto = cantidad * (this.ultimo_precio_plata/28.3495);
    }
      
    //$('#'+id_monto).val(number_format(Math.ceil10(monto, -2), 2, ',', '.'));
    $('#'+id_monto).val(Math.ceil10(monto, -2));
}

/**
 * Resetea la cantidad  de la transacción en un input
 * @param {int} id_cantidad : id del input a colocar la cantidad
 * @param {int} id_monto : id del input a sacar el monto
 * @param {int} id_tipo : id del input que me indica el tipo de gramo
 */
function resetCantidad(id_cantidad,id_monto,id_tipo){

    var monto = $('#'+id_monto).val();

    if($('#'+id_tipo).val() === 'oro'){
        //var monto = cantidad * (this.ultimo_precio_oro/28.3495);
        var cantidad = monto / (this.ultimo_precio_oro/28.3495);
    }else
    if($('#'+id_tipo).val() === 'plata'){
        //var monto = cantidad * (this.ultimo_precio_plata/28.3495);
        var cantidad = monto / (this.ultimo_precio_plata/28.3495);
    }
      
    //$('#'+id_cantidad).val(number_format(Math.ceil10(monto, -2), 2, ',', '.'));
    $('#'+id_cantidad).val(Math.ceil10(cantidad, -2));
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

