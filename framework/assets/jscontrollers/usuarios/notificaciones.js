/**
 * Marca una notificación como leída
 * @param {*} id_notificacion : id de de la notificación
 */
function mark_as_read(id_notificacion) {

    $('#mensaje_'+id_notificacion).css("background-color", "#f8fafb");
    $('#boton_'+id_notificacion).attr('disabled','disabled');

    $.ajax({
        type: "POST",
        url: "api/set/notifications",
        data: {"id_notificacion":id_notificacion}
    });
}