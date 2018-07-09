/**
 * Elimina un elemento
 * 
 * @param {*} id_elemento 
 * @param {*} controlador 
 */
function delete_sucursal(id_elemento) {
    swal({   
        title: "¿Está seguro?",   
        text: "¡Usted está a punto de borrar este elemento!",   
        type: "warning",   
        buttons: {
            cancel: {
              text: "Cancelar",
              value: null,
              visible: true,
              className: "",
              closeModal: true,
            },
            confirm: {
              text: "Confirmar",
              value: true,
              visible: true,
              className: "btn-primary",
              closeModal: true
            }
          }
    }).then((value) => {
        if (value == true){
            swal("Eliminado!", "El elemento ha sido eliminado.", "success"); 
            setTimeout(function(){
                location.href = 'sucursal/eliminar/' + id_elemento.toString();
            },1000);
        }
      });;
}