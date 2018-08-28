/**
 * Abre el modal para cargar los documentos
 */
function cargardocumentos() {




    $('#cargarDocumentosModal').modal('show');
}


/**
 * Ajax action to api rest
*/
function uploaddocumentos(){
    
    var $ocrendForm = $(this), __data = {};
        if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {

            var paqueteDeDatos = new FormData();
           
            paqueteDeDatos.append('foto_documento', $('#id_foto_documento')[0].files[0]);
            paqueteDeDatos.append('foto_pasaporte', $('#id_foto_pasaporte')[0].files[0]);
            paqueteDeDatos.append('foto_rif', $('#id_foto_rif')[0].files[0]);
            paqueteDeDatos.append('foto_ref1', $('#id_foto_ref1')[0].files[0]);
            paqueteDeDatos.append('foto_ref2', $('#id_foto_ref1')[0].files[0]);

            $('#cargardocumentosbtn').attr('disabled','disabled');
            $.ajax({
                type : "POST",
                url : "api/usuarios/subirdocumentos",
                contentType: false,
                processData: false,
                dataType: 'json',
                data : paqueteDeDatos,
                beforeSend: function(){ 
                    $ocrendForm.data('locked', true) 
                },
                success : function(json) {
                    if(json.success == 1) {
                        setTimeout(function(){                         
                            toastr.success(json.message);
                            location.href='perfilcliente/';
                        },1000);
                    } else {
                        toastr.error(json.message);
                    }
                },
                error : function(xhr, status) {
                    toastr.error('Ha ocurrido un problema interno');
                },
                complete: function(){ 
                    $('#cargardocumentosbtn').removeAttr('disabled');
                    $ocrendForm.data('locked', false);
                } 
            });
        }

    
} 

/**
 * Events
 */
$('#cargardocumentosbtn').click(function(e) {
    e.defaultPrevented;
    uploaddocumentos();
});
$('form#cargar_documentos_form input').keypress(function(e) {
    e.defaultPrevented;
    if(e.which == 13) {
        uploaddocumentos();

        return false;
    }
});
