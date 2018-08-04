/**
 * Swal para concretar una orden
 * @param {*} id_orden : id de la orden a concretar
 */
function specify_orden(id_orden){

    swal({
        title: "¿Está seguro que desea concretar la orden?",
        text: "",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#54dd7b",
        confirmButtonText: "¡Si, quiero concretar!",
        closeOnConfirm: false
    }, function () {
        swal("Exito!", "La orden ha sido concretada", "success");
        setTimeout(function(){
            location.href = 'ordenadmin/concretar/' + id_orden.toString();
        },1000);
    });


}