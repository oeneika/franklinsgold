/**
 * Resetea el precio de la divisa inicial mostrando el de la actual
 */
function changePrice(){

    var price = $('#id_divisa_inicial').val();
    $('#id_precio_divisa_inicial').val(price);

    $('#id_cantidad_divisa_inicial').val(1);
    $('#id_monto_inicial').val(number_format(price,2, ',', '.'));

}

/**
 * Resetea el monto inicial segun la cantidad y el precio inicial
 */
function changeMontoInicial(){

    var price = $('#id_precio_divisa_inicial').val();
    var quantity = $('#id_cantidad_divisa_inicial').val();

    $('#id_monto_inicial').val(number_format(price*quantity,2, ',', '.'));

}
 

/**
 * Hace la conversión
 */
function calculadora(){

    var initial_price = $('#id_precio_divisa_inicial').val();
    var quantity = $('#id_cantidad_divisa_inicial').val();

    var final_price = $('#id_divisa_final').val();

    var equivalent = (initial_price/final_price)*quantity;
    $('#id_monto_final').val(number_format(equivalent,2, ',', '.'));

 }

 
/**
 * Events
 *  
 * @param {*} e 
 */
$('#convertirDivisabtn').click(function (e) {
    e.defaultPrevented;
    calculadora();
});

 



//monto = cantidad(gramos) * (precio / 28.3495)  


