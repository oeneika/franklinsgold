//Muestra o oculta el mini menu para los vendedores y supervisores

//Crear usuarios
$('input[type=radio][class=tipo_crear]').on('change',function (e){
    if ( ($('input[type=radio][class=tipo_crear]:checked').val() == 1) || ($('input[type=radio][class=tipo_crear]:checked').val() == 3) ){
        $('.selects_body').show();
    }
    else{
        $('.selects_body').hide();
    }
})

//Editar usuarios
$('input[type=radio][class=tipo_editar]').on('change',function (e){
    if ( ($('input[type=radio][class=tipo_editar]:checked').val() == 1) || ($('input[type=radio][class=tipo_editar]:checked').val() == 3)  ){
        $('.selects_body').show();
        $('.selects_body2').hide();
    }
    else if ($('input[type=radio][class=tipo_editar]:checked').val() == 2){
        $('.selects_body2').show();
        $('.selects_body').hide();
    }
    else{
        $('.selects_body').hide();
        $('.selects_body2').hide();
    }
})