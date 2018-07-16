function fillTel(id){
    $('#telefonos_edit').empty();
    $.ajax({
        type : "GET",
        url : "api/afiliados/getTelefonos/"+id,
        success : function(json) {
            for (let i = 0; i < json.length; i++) {
                var button = numTelefonos == 1?`<a style="font-size:22px;" title="Agregar Telefono" onclick="addTel(true)"><i class="fa fa-plus naranja"></i></a>`:`<a style="font-size:22px;" title="Agregar Telefono" onclick="delTel(${numTelefonos})"><i class="fa fa-trash naranja"></i></a>`
                $('#telefonos_edit').append(
                    `<div class="row" id="tel_${numTelefonos}">
                        <div class="col-md-10 col-xs-10">
                            <div class="form-group">
                                <input type="tel" class="form-control" name="telefono[${numTelefonos}]" value="${json[i].telefono}">
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            ${button}
                        </div>
                    </div>`);
                    numTelefonos++;
            }
        },
        error : function(xhr, status) {
            toastr.error('Ha ocurrido un problema interno');
        }
    });
}

/**
 * Abre el modal para un nuevo afiliados
 */
function editarAfiliado(id,nombre,direccion,sucursal) {
    numTelefonos = 1;
    $('#id_afiliados_edit').val(id);
    $('#id_nombre_edit').val(nombre);
    $('#id_sucursal_edit').val(sucursal);
    $('#id_direccion_edit').val(direccion);
    $('#editarAfiliado').modal('show');
    fillTel(id);
}

/**
 * Ajax action to api rest
*/
function editar_afiliados(){
    var $ocrendForm = $(this), __data = {};
    $('#editar_afiliado_form').serializeArray().map(function(x){__data[x.name] = x.value;}); 

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        $('#editarafiliadobtn').attr('disabled','disabled');
        $.ajax({
            type : "POST",
            url : "api/afiliados/editar",
            dataType: 'json',
            data : __data,
            beforeSend: function(){ 
                $ocrendForm.data('locked', true) 
            },
            success : function(json) {
                if(json.success == 1) {
                    setTimeout(function(){
                        toastr.success(json.message);
                        location.href='afiliados/afiliados/';
                    },1000);
                } else {
                    toastr.error(json.message);
                }
            },
            error : function(xhr, status) {
                toastr.error('Ha ocurrido un problema interno');
            },
            complete: function(){ 
                $('#editarafiliadobtn').removeAttr('disabled');
                $ocrendForm.data('locked', false);
            } 
        });
    }
} 

/**
 * Events
 */
$('#editarafiliadobtn').click(function(e) {
    e.defaultPrevented;
    editar_afiliados();
});
$('form#editar_afiliado_form input').keypress(function(e) {
    e.defaultPrevented;
    if(e.which == 13) {
        editar_afiliados();

        return false;
    }
});
