/**
 * Elimina un elemento
 * 
 * @param {*} id_elemento 
 * @param {*} controlador 
 */
function delete_item(id_elemento, controlador) {
    toastr.info({   
        title: "¿Está seguro?",   
        text: "¡Usted está a punto de borrar este elemento!",   
        type: "warning",   
        showCancelButton: true,   
        confirmButtonColor: "#DD6B55",   
        confirmButtonText: "Sí, Eliminar!", 
        cancelButtonText: "No, Cerrar!",     
        closeOnConfirm: false 
    }, function(){   
        toastr.info("Elimindo!", "El elemento ha sido eliminado.", "success"); 
        setTimeout(function(){
            location.href = controlador + '/eliminar/' + id_elemento.toString();
        },1000);
    });
}