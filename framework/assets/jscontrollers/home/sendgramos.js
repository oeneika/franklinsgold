/**
 * Envía los gramos a Franklin desde un comercio afiliado
 */
function send_gramos() {
    swal({
        title: "¿Está seguro que desea enviar los gramos del comercio afiliado?",
        text: "Los gramos se descontarán del comercio afiliado",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "¡Si, quiero enviar!",
        closeOnConfirm: false
    }, function () {
        swal("!Exito!", "Los gramos del comercio afiliado han sido enviados", "success");
        setTimeout(function(){
            location.href = 'home/sendgramos/';
        },1000);
    });
}