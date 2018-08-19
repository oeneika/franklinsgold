/**
 * Abre el modal para un nuevo Origen
 */
function crearDivisa() {
    $('#crearDivisa').modal('show');
}

/**
 * Ajax action to api rest
*/

function createDivisa(formulario) {
    var $ocrendForm = $(this), __data = {};
    $('#'+formulario).serializeArray().map(function(x){__data[x.name] = x.value;}); 

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        $.ajax({
            type : "POST",
            url : "api/divisa/crear",
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
$('#crearDivisabtn').click(function (e) {
    e.defaultPrevented;
    createDivisa('crear_divisa_form');
});
$('crear_divisa_form').keypress(function (e) {
    e.defaultPrevented;
    if (e.which == 13) {
        createDivisa('crear_divisa_form');
        return false;
    }
});
