//Muestra o oculta el mini menu para los vendedores
$('input[type=radio][class=tipo_crear]').on('change',function (e){
    if ($('input[type=radio][class=tipo_crear]:checked').val() == 1){
        $('.selects_body').show();
    }
    else{
        $('.selects_body').hide();
    }
})

$('input[type=radio][class=tipo_editar]').on('change',function (e){
    if ($('input[type=radio][class=tipo_editar]:checked').val() == 1){
        $('.selects_body').show();
    }
    else{
        $('.selects_body').hide();
    }
})