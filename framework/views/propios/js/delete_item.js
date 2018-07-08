/**
 * Elimina un elemento
 * 
 * @param {*} id_elemento 
 * @param {*} controlador 
 */
function delete_item(id_elemento, controlador) {
    swal({
        title: "¿Estás seguro de eliminar este elemento?",
        text: "Una vez eliminado no podrás recuperarlo",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "¡Si, quiero eliminar!",
        closeOnConfirm: false
    }, function () {
        swal("Eliminado!", "Tu archivo fue eliminado, exitosamente", "success");
    });
}