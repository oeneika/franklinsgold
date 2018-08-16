
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
    $('#registro_form').serializeArray().map(function(x){__data[x.name] = x.value;}); 

        if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
            $('#registro').attr('disabled','disabled');
            $.ajax({
                type : "POST",
                url : "api/register",
                dataType: 'json',
                data : __data,
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
