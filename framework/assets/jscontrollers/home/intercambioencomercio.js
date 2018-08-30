
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
function showModal(ultimo_precio_oro,ultimo_precio_plata,precio_BsS,modal) {
    this.ultimo_precio_oro = ultimo_precio_oro/*/28.3495*/;
    this.ultimo_precio_plata = ultimo_precio_plata/*/28.3495*/;
    this.precio_bolivar_soberano = precio_BsS;

    $('#'+modal).modal('show');
    
}


/**
 * Resetea la cantidad de gramos en el input
 * @param {int} id_cantidad_BsS : id del input con la cantidad de BsS
 * @param {int} id_tipo : id del input que me indica el tipo de gramo
 * @param {int} id_cantidad_gramos : id del input donde se colocará la cantidad de gramos
 * @param {int} id_monto_dolares : id del input donde se colocará la cantidad de dólares
 */
function resetQuantity(id_cantidad_BsS,id_tipo,id_cantidad_gramos){

    var cantidad_BsS = $('#'+id_cantidad_BsS).val();

    if($('#'+id_tipo).val() === 'oro'){
        
        var cantidad = cantidad_BsS * (precio_bolivar_soberano / this.ultimo_precio_oro);
    }else
    if($('#'+id_tipo).val() === 'plata'){
        
        var cantidad = cantidad_BsS * (precio_bolivar_soberano / this.ultimo_precio_plata);
    }
      
    $('#'+id_cantidad_gramos).val(Math.ceil10(cantidad, -2));
}


/**
 * Ajax action to api rest
*/
function createIntercambioComercio(formulario) {
    var $ocrendForm = $(this), __data = {};
    $('#'+formulario).serializeArray().map(function(x){__data[x.name] = x.value;}); 

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        $.ajax({
            type : "POST",
            url : "api/orden/intercambiocomercio",
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
    
                    toastr.success(json.message,'Exito!');
                    
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
 * Ajax action to api rest
*/
function concreteIntercambioComercio(formulario) {
    var $ocrendForm = $(this), __data = {};
    $('#'+formulario).serializeArray().map(function(x){__data[x.name] = x.value;}); 

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        $.ajax({
            type : "POST",
            url : "api/orden/concretarintercambiocomercio",
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
 * Events
 *  
 * @param {*} e 
 */
$('#crearIntercambioEnComercioBtn').click(function (e) {
    e.defaultPrevented;
    createIntercambioComercio('crearIntercambioComercio_form');
});
$('#crearIntercambioComercio_form').keypress(function (e) {
    e.defaultPrevented;
    if (e.which == 13) {
        createIntercambioComercio('crearIntercambioComercio_form');
        return false;
    }
});

$('#concretarIntercambioEnComercioBtn').click(function (e) {
    e.defaultPrevented;
    concreteIntercambioComercio('concretarIntercambioComercio_form');
});
$('#concretarIntercambioComercio_form').keypress(function (e) {
    e.defaultPrevented;
    if (e.which == 13) {
        concreteIntercambioComercio('concretarIntercambioComercio_form');
        return false;
    }
});