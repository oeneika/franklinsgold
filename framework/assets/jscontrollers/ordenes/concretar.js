/**
 * Swal para concretar una orden
 * @param {*} id_orden : id de la orden a concretar
 */
function specify_orden(id_orden){

    swal({
        title: "¿Está seguro que desea concretar la orden?, se ejecutará la transacción",
        text: "",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#54dd7b",
        confirmButtonText: "¡Si, quiero concretar!",
        closeOnConfirm: false
    }, function () {
        swal("¡Éxito!", "La orden ha sido concretada", "success");
        setTimeout(function(){
            location.href = 'ordenadmin/concretar/' + id_orden.toString();
        },1000);
    });

}


/**
 * Swal para confirmar una orden
 * @param {*} id_orden : id de la orden a confirmar
 * @param {*} tipo_usuario : tipo de usuario que confirmará la orden
 */
function confirm_orden(id_orden,tipo_usuario){

    swal({
        title: "¿Está seguro que desea confirmar esta orden como " + tipo_usuario + "?",
        text: "",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#54dd7b",
        confirmButtonText: "¡Sí, quiero confirmar!",
        closeOnConfirm: false
    }, function () {
        swal("¡Éxito!", "La orden ha sido confirmada", "success");
        setTimeout(function(){
            location.href = 'ordenadmin/confirmar/' + id_orden.toString();
        },1000);
    });


}