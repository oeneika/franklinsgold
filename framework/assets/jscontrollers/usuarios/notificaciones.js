/**
 * Marca una notificación como leída
 * @param {*} id_notificacion : id de de la notificación
 */
function mark_as_read(id_notificacion) {

    $('#mensaje_'+id_notificacion).css("background-color", "#e7e7e7");
    $('#boto_'+id_notificacion).css("color", "#e7e7e7");
    $('#boton_'+id_notificacion).attr('disabled','disabled');

    $.ajax({
        type: "POST",
        url: "api/set/notifications",
        data: {"id_notificacion":id_notificacion}
    });
}