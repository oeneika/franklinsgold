function showModal(modal,ultimo_precio_oro,ultimo_precio_plata,precio_BsS) {
    this.ultimo_precio_oro = ultimo_precio_oro/28.3495;
    this.ultimo_precio_plata = ultimo_precio_plata/28.3495;
    this.precio_bolivar_soberano = precio_BsS;

    $('#'+modal).modal('show');
}