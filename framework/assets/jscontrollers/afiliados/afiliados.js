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
                    <input type="tel" class="form-control" name="telefono[${numTelefonos}]" >
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

function showIntercambios(id){
    $('#body_intercambios').empty();
    $('#crearnuevointercambiobtn').attr('onclick',`crearintercambio(${id})`);
    $.ajax({
        type : "GET",
        url : "api/afiliados/getIntercambios/"+id,
        success : function(json) {
            console.log(json);
            for (let i = 0; i < json['intercambios'].length; i++) {
                $('#body_intercambios').append(
                `<tr>
                    <td>${json['intercambios'][i].codigo}</td>
                    <td>${date('F j, Y',json['intercambios'][i].fecha)}</td>
                    <td>$${number_format(json['intercambios'][i].monto,2,'.',',')}</td>
                </tr>`)  
            }
            $("#total_intercambio").html(`$${number_format(json['total'][0].total,2,'.',',')}`);
        },
        error : function(xhr, status) {
            toastr.error('Ha ocurrido un problema interno');
        },
        complete: function(){ 
            $("#intercambioAfiliados").modal('show');
        } 
    });
}

function crearintercambio(id){
    $("#intercambioAfiliados").modal('hide');
    $('#id_intercambio_comercio').val(id)
    $("#crearIntercambio").modal('show');
}