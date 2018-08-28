/**
 * Abre el modal para un nuevo Rango
 */
function crearRango() {
    $('#crearRango').modal('show');
}

/**
 * Ajax action to api rest
*/

function createRango(formulario) {
    var $ocrendForm = $(this), __data = {};
    $('#'+formulario).serializeArray().map(function(x){__data[x.name] = x.value;}); 

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        $.ajax({
            type : "POST",
            url : "api/rango/crear",
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
                        timeOut: 1000
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
$('#crearrangobtn').click(function (e) {
    e.defaultPrevented;
    createRango('crear_rango_form');
});
$('crear_rango_form').keypress(function (e) {
    e.defaultPrevented;
    if (e.which == 13) {
        createRango('crear_rango_form');
        return false;
    }
});
