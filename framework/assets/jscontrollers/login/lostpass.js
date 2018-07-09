/**
 * Ajax action to api rest
*/
function lostpass(){
    
    var $ocrendForm = $(this), __data = {};
    $('#lostpass_form').serializeArray().map(function(x){__data[x.name] = x.value;}); 

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        $.ajax({
            type : "POST",
            url : "api/lostpass",
            dataType: 'json',
            data : __data,
            beforeSend: function(){ 
                $ocrendForm.data('locked', true) 
            },
            success : function(json) {
                if(json.success == 1) {
                    setTimeout(function(){
                        toastr.success(json.message);
                        location.href='home/';
                    },1000);
                } else {
                    toastr.error(json.message);
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
 */
$('#recuperarbtn').click(function(e) {
    e.defaultPrevented;
    lostpass();
});
$('form#lostpass_form input').keypress(function(e) {
    e.defaultPrevented;
    if(e.which == 13) {
        lostpass();

        return false;
    }
});
