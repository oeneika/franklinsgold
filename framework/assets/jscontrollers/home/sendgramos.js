/**
 * Envía los gramos a Franklin desde un comercio afiliado
 */
function sendGramos(formulario) {
    var $ocrendForm = $(this), __data = {};
    $('#'+formulario).serializeArray().map(function(x){__data[x.name] = x.value;}); 

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        $.ajax({
            type : "POST",
            url : "api/sendGramos",
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
$('#crearEnvioGramosBtn').click(function (e) {
    e.defaultPrevented;
    sendGramos('enviargramos_form');
});
$('#enviargramos_form').keypress(function (e) {
    e.defaultPrevented;
    if (e.which == 13) {
        sendGramos('enviargramos_form');
        return false;
    }
});