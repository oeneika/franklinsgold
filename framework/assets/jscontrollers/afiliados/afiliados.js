var numTelefonos = 1;



/**
 * Agrega telefonos al formulario
 */
function addTel(edit = false){
    var id_dom = edit == true? '#telefonos_edit':'#telefonos'
    $(id_dom).append(
        `<div class="row" id="tel_${numTelefonos}">
            <div class="col-md-10 col-xs-10">
                <div class="form-group">
                    <input name="telefono[${numTelefonos}]" type="number" class="form-control" aria-required="true" aria-invalid="false" placeholder="">
                </div>
            </div>
            <div class="col-md-2 col-xs-2">
                <a style="font-size:22px;" title="Agregar Telefono" onclick="delTel(${numTelefonos})"><i class="fa fa-trash naranja"></i></a>
            </div>
        </div>`);
    numTelefonos++;
}

/**
 * Elimina un telefono del formulario
 */
function delTel(id){
    numTelefonos--;
    $(`#tel_${id}`).remove();
}

/**
 * Ajax para traer telefonos
 */
function getTel(id){
    $('#listado_telefonos').empty();
    $.ajax({
        type : "GET",
        url : "api/afiliados/getTelefonos/"+id,
        success : function(json) {
            for (let i = 0; i < json.length; i++) {
                $('#listado_telefonos').append(`<li>${json[i].telefono}</li>`)  
            }
        },
        error : function(xhr, status) {
            toastr.error('Ha ocurrido un problema interno');
        },
        complete: function(){ 
            $('#modaltelefonos').modal('show');
        } 
    });
}