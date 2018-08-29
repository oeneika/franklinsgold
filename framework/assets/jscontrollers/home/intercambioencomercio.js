/**
 * 
 * @param {string} modal : nombre del modal a mostrar
 */
function showModal(modal){
    $('#'+modal).modal('show');
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