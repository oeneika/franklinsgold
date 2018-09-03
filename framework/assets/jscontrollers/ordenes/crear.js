/**
 * Variables que contienen los precios actuales del oro y de la plata y del BsS
 */
var ultimo_precio_oro=0;
var ultimo_precio_plata=0;
var precio_bolivar_soberano=0;

/**
 * 
 * @param {int} ultimo_precio_oro : precio actual del oro
 * @param {int} ultimo_precio_plata : precio actual de la plata
 * @param {int} precio_BsS : precio del BsS almacenado en la base de datos
 */
function showModal(ultimo_precio_oro,ultimo_precio_plata,precio_BsS,modal=null) {
    this.ultimo_precio_oro = ultimo_precio_oro/*/28.3495*/;
    this.ultimo_precio_plata = ultimo_precio_plata/*/28.3495*/;
    this.precio_bolivar_soberano = precio_BsS;


    if(modal!=null){
        $('#'+modal).modal('show');
    }
}


/**
 * Resetea la cantidad de gramos en el input
 * @param {int} id_cantidad_BsS : id del input con la cantidad de BsS
 * @param {int} id_tipo : id del input que me indica el tipo de gramo
 * @param {int} id_cantidad_gramos : id del input donde se colocará la cantidad de gramos
 * @param {int} id_monto_dolares : id del input donde se colocará la cantidad de dólares
 */
function resetQuantity(id_cantidad_BsS,id_tipo,id_cantidad_gramos,id_monto_dolares){

    var cantidad_BsS = $('#'+id_cantidad_BsS).val();

    if($('#'+id_tipo).val() === 'oro'){
        
        var cantidad = cantidad_BsS * (precio_bolivar_soberano / this.ultimo_precio_oro);
    }else
    if($('#'+id_tipo).val() === 'plata'){
        
        var cantidad = cantidad_BsS * (precio_bolivar_soberano / this.ultimo_precio_plata);
    }
      
    $('#'+id_monto_dolares).val(Math.ceil10(cantidad_BsS*precio_bolivar_soberano, -2));
    $('#'+id_cantidad_gramos).val(Math.ceil10(cantidad, -2));
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
    
                    toastr.success(json.message,'¡Éxito!');
                    
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
$('#crearOrdenCompraVentaTienda').click(function(e) {
    e.defaultPrevented;
    createOrden('crearOrdenCompraVentaTienda_form');
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
$('form#crearOrdenCompraVentaTienda_form input').keypress(function(e) {
    e.defaultPrevented;
    if(e.which == 13) {
        createOrden('crearOrdenCompraVentaTienda_form');
        return false;
    }
});

