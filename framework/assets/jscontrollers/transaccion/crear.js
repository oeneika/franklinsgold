//Variables usadas para la interaccion entre selects
var userDisableds1;
var userDisableds2;
var monDisableds1;
var monDisableds2;

/**
 * Validad las opciones entre los selects de usuarios para que no se seleccione uno mismo en los dos
 * @param {*} id_select 
 * @param {*} id_select2 
 */
function disableSelectOption(id_select,id_select2){

        $("#id_select").select2({
            dropdownCssClass:'increasezindex',
            width: 'style', 
            disabled:'true',
            dropdownParent: $('.modal')
        });

    if(id_select === "id_id_usuario"){

         //Reactiva la ultima opcion desactivada
        $("#"+id_select2+ " option[value=" + userDisableds2 + "]").removeAttr('disabled');

        //Guarda el id de la opcion a desactivar en id_select2
        userDisableds2 = $('#'+id_select).val();

        //Desactiva la opcion en el id_select2
        $("#"+id_select2+ " option[value=" + userDisableds2 + "]").attr("disabled","disabled");   

    }else{

        //Reactiva la ultima opcion desactivada
        $("#"+id_select2+ " option[value=" + userDisableds1 + "]").removeAttr('disabled');

        //Guarda el id de la opcion a desactivar en id_select2
        userDisableds1 = $('#'+id_select).val();

        //Desactiva la opcion en el id_select2
        $("#"+id_select2+ " option[value=" + userDisableds1 + "]").attr("disabled","disabled");   
    }
}


/**
 * Validad las opciones entre los selects de monedas para que no se seleccione una mismo en los dos
 * @param {*} id_select 
 * @param {*} id_select2 
 */
function disableSelectOptionMon(id_select,id_select2){

    if(id_select == 'id_id_moneda'){

         //Reactiva la ultima opcion desactivada
        $("#"+id_select2+ " option[value=" + monDisableds2 + "]").removeAttr('disabled');

        //Guarda el id de la opcion a desactivar en id_select2
        monDisableds2 = $('#'+id_select).val();

        //Desactiva la opcion en el id_select2
        $("#"+id_select2+ " option[value=" + monDisableds2 + "]").attr("disabled","disabled");   

    }else{

        //Reactiva la ultima opcion desactivada
        $("#"+id_select2+ " option[value=" + monDisableds1 + "]").removeAttr('disabled');

        //Guarda el id de la opcion a desactivar en id_select2
        monDisableds1 = $('#'+id_select).val();

        //Desactiva la opcion en el id_select2
        $("#"+id_select2+ " option[value=" + monDisableds1 + "]").attr("disabled","disabled");   
    }
}



/**
 * Abre el modal para una nueva transaccion
 */
function crearTransaccion(tipo) {


    $('#tipo_transaccion').val(tipo);

    if(tipo == 3){
        $('#crearIntercambio').modal('show');
    }else{
        $('#crearTransacciones').modal('show');
    }

    
}

/**
 * Ajax action to api rest
*/
function createTransaccion() {
    $('#creartransaccionesbtn').attr('disabled','disabled');
    $.ajax({
        type: "POST",
        url: "api/transaccion/crear",
        data: $('#crear_transacciones_form').serialize(),
        success: function (json) {

            if(json.success == 1) {

                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    showMethod: 'slideDown',
                    timeOut: 4000
                };

                toastr.success('¡Transacción creada!','¡Éxito!');
                
                setTimeout(function () {
                    location.reload();
                }, 1000);
                
            }else {
                toastr.error(json.message, '¡Ups!');
            }

        },
        error: function (xhr, status) {

            toastr.error("Ha ocurrido un problema", '¡ERROR!');

            toastr.options = {
                "closeButton": false,
                "debug": false,
                "progressBar": false,
                "preventDuplicates": false,
                "positionClass": "toast-top-right",
                "onclick": null,
                "showDuration": "400",
                "hideDuration": "1000",
                "timeOut": "3000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }
        },
        complete: function () {
          $('#creartransaccionesbtn').removeAttr('disabled');
        }
    });
}


/**
 * Events
 *  
 * @param {*} e 
 */
$('#creartransaccionesbtn').click(function (e) {
    e.defaultPrevented;
    createTransaccion();
});
$('crear_transacciones_form').keypress(function (e) {
    e.defaultPrevented;
    if (e.which == 13) {
        createTransaccion();
        return false;
    }
});

$('#selector_comercio').on('change',function(e){
    e.defaultPrevented;
    usuarios.forEach(element => {
        if(element.id_comercio_afiliado == $(this).val() && $('#selector_usuario').val() == element.id_user){
            toastr.error('El usuario que eligio pertenece a este comercio', '¡Ups!');
            $(this).val(null).change();
        }
    });
});

$('#selector_sucursal').on('change',function(e){
    e.defaultPrevented;
    usuarios.forEach(element => {
        if(element.id_sucursal == $(this).val() && $('#selector_usuario').val() == element.id_user){
            toastr.error('El usuario que eligio pertenece a esta sucursal', '¡Ups!');
            $(this).val(null).change();
        }
    });
});
