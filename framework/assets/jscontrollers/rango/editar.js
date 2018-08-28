/**
 *  Abre el modal para editar un rango
 * @param {*} id_rango : id del rango
 * @param {*} nombre_rango : nombre del rango
 * @param {*} monto_diario : monto diario del rango 
 */
function editarRango(id_rango,nombre_rango,monto_diario) {

    $('#id_id_rango').val(id_rango);
    //$('#id_nombre_rango').val(nombre_rango);
    $('#id_monto_diario').val(monto_diario);


    $('#editarRango').modal('show');

}

/**
 * Ajax action to api rest
*/
function editRango(formulario) {
    var $ocrendForm = $(this), __data = {};
    $('#'+formulario).serializeArray().map(function(x){__data[x.name] = x.value;}); 

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        $.ajax({
            type : "POST",
            url : "api/rango/editar",
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
$('#editarrangobtn').click(function (e) {
    e.defaultPrevented;
    editRango('editar_rango_form');
});
$('form#editar_rango_form input').keypress(function (e) {
    e.defaultPrevented;
    if (e.which == 13) {
        editRango('editar_rango_form');

        return false;
    }
});