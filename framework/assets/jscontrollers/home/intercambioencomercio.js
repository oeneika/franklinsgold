

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

                    $("#myModalcito").modal("show");

                    //Agrega al body la imagen de el código qr
                    $( ".modal-body" ).append( `
                    <div class="codigqr_intercambiocomercio" align="center" width="100%">
                        <a href="${ json.message }" target=_blank rel='noopener noreferrer'><img src="${ json.message }" alt='Codigo QR' class='img-thumbnail'></a>
                    </div>
                    ` );

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
    
                    toastr.info(json.message,'Éxito!');
                    
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
$('#cerrarIntercambioModal').click(function (e) {
    e.defaultPrevented;

    setTimeout(function () {
        location.reload();
    }, 500);

    //Muestra los elementos  del body
   /* $( ".clientes_intercambiocomercio" ).show();
    $( ".cantidadbss_intercambiocomercio" ).show();
    $( ".tipo_intercambiocomercio" ).show();
    $( ".cantidad_intercambiocomercio" ).show();

    //Oculta la imagen qr del body
    $( ".codigqr_intercambiocomercio" ).hide();

    //Muestra el boton de crear
    $( "#crearIntercambioEnComercioBtn" ).show();*/
});