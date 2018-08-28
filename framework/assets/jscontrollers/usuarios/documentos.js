function documentosUsuario(documento_identidad,pasaporte,rif,referencia_bancaria_1,referencia_bancaria_2) {
    $('#bodyDocumentos').empty();
    $( "#bodyDocumentos" ).append( 
        `
        <tr>
            <td>
                ${ documento_identidad == '' ? 'No posee' : 
                '<a href="'+ documento_identidad +'" target=_blank rel="noopener noreferrer"><img src="'+ documento_identidad +'"  class="img-thumbnail" style="width:75px;"></a>' }
            </td>

            <td>                                                 
                ${ pasaporte == '' ? 'No posee' : 
                '<a href="'+ pasaporte +'" target=_blank rel="noopener noreferrer"><img src="'+ pasaporte +'"  class="img-thumbnail" style="width:75px;"></a>' }
            </td>

            <td>
                ${ rif == '' ? 'No posee' : 
                '<a href="'+ rif +'" target=_blank rel="noopener noreferrer"><img src="'+ rif +'"  class="img-thumbnail" style="width:75px;"></a>' }                                                
            </td>

            <td>
                ${ referencia_bancaria_1 == '' ? 'No posee' : 
                '<a href="'+ referencia_bancaria_1 +'" target=_blank rel="noopener noreferrer"><img src="'+ referencia_bancaria_1 +'"  class="img-thumbnail" style="width:75px;"></a>' }                                                    
            </td>

            <td>
                ${ referencia_bancaria_2 == '' ? 'No posee' : 
                '<a href="'+ referencia_bancaria_2 +'" target=_blank rel="noopener noreferrer"><img src="'+ referencia_bancaria_2 +'"  class="img-thumbnail" style="width:75px;"></a>' }                                                    
            </td>
        </tr>  
        `
     );
    
    
    
    $('#verDocumentosModal').modal('show');

}