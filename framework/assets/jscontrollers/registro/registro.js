/**
 * Abre el modal con los términos y condiciones
 */
function openTerminos() {
    var terminos = $('#terminos_ocultos').val();
   
    $( ".texto_terminos" ).append( "<p>" + terminos + "</p>" );

    $('#modal_terminos').modal('show');
}


/**
 * Ajax action to api rest
*/
function registro(){

    if( $('#id_terminos').prop('checked') ) {
        
    var $ocrendForm = $(this), __data = {};
    //$('#registro_form').serializeArray().map(function(x){__data[x.name] = x.value;}); 

        if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {

            var paqueteDeDatos = new FormData();
           
            paqueteDeDatos.append('foto_documento_identidad', $('#id_foto_documento_identidad')[0].files[0]);
            paqueteDeDatos.append('foto_pasaporte', $('#id_foto_pasaporte')[0].files[0]);

            paqueteDeDatos.append('primer_nombre', $('#id_primer_nombre').prop('value'));
            paqueteDeDatos.append('segundo_nombre', $('#id_segundo_nombre').prop('value'));
            paqueteDeDatos.append('primer_apellido', $('#id_primer_apellido').prop('value'));
            paqueteDeDatos.append('segundo_apellido', $('#id_segundo_apellido').prop('value'));
            paqueteDeDatos.append('sexo', $('#id_sexo').prop('value'));
            paqueteDeDatos.append('usuario', $('#id_usuario').prop('value'));
            paqueteDeDatos.append('email', $('#id_email').prop('value'));
            paqueteDeDatos.append('telefono', $('#id_telefono').prop('value')); 
            paqueteDeDatos.append('numero_cedula', $('#id_numero_cedula').prop('value'));  
            paqueteDeDatos.append('pin', $('#id_pin').prop('value'));    
            paqueteDeDatos.append('pin_re', $('#id_pin_rep').prop('value'));    
            paqueteDeDatos.append('pass', $('#id_pass').prop('value'));
            paqueteDeDatos.append('pass_repeat', $('#id_pass_repeat').prop('value')); 
            paqueteDeDatos.append('nombre_banco', $('#id_nombre_banco').prop('value')); 
            paqueteDeDatos.append('numero_cuenta', $('#id_numero_cuenta').prop('value'));

            $('#registro').attr('disabled','disabled');
            $.ajax({
                type : "POST",
                url : "api/register",
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
                            location.href='login/';
                        },1000);
                    } else {
                        toastr.error(json.message);
                    }
                },
                error : function(xhr, status) {
                    toastr.error('Ha ocurrido un problema interno');
                },
                complete: function(){ 
                    $('#registro').removeAttr('disabled');
                    $ocrendForm.data('locked', false);
                } 
            });
        }

    }else{
        toastr.error('Debe aceptar los términos y condiciones para proceder con el registro.');
    }
    
} 

/**
 * Events
 */
$('#registro').click(function(e) {
    e.defaultPrevented;
    registro();
});
$('form#registro_form input').keypress(function(e) {
    e.defaultPrevented;
    if(e.which == 13) {
        registro();

        return false;
    }
});

$('#buttonTerminos').click(function(e) {
    e.defaultPrevented;
    openTerminos();
});
