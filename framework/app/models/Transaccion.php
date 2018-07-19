<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\models;

use app\models as Model;
use Ocrend\Kernel\Helpers as Helper;
use Ocrend\Kernel\Models\Models;
use Ocrend\Kernel\Models\IModels;
use Ocrend\Kernel\Models\ModelsException;
use Ocrend\Kernel\Models\Traits\DBModel;
use Ocrend\Kernel\Router\IRouter;
use Ocrend\Kernel\Helpers\Functions;
use Ocrend\Kernel\Helpers\Emails;

/**
 * Modelo Transaccion
 */
class Transaccion extends Models implements IModels {
    
    use DBModel;
    private $id_usuario;
    private $id_usuario2;
    private $codigo_moneda;
    private $codigo_moneda2;
    private $id_sucursal;
    private $id_comercio;
    private $tipo;


    /**
     * Envía un email de compra o venta exitosa al usuario que realizó la operación
     * 
     * @param HTML :  Mensaje escrito en html a mostrar en el correo
     * @param email :  email del usuario que realiza la transacción
     * @param nombre :  nombre del usuario que realiza la transacción
     * @param apellido :  apellido del usuario que realiza la transacción
     * 
     */
    private function sendSuccesMail(string $HTML, string $email, string $nombre = "", string $apellido = "") {
        global $config;

        $dest = array();
        $dest[$email] = $nombre .' '. $apellido;

        Emails::send($dest,array(
            # Título del mensaje
            '{{title}}' =>  $config['build']['name'],
            # Url de logo
            '{{url_logo}}' => $config['build']['url'],
            # Logo
            '{{logo}}' => $config['mailer']['logo'],
            # Contenido del mensaje
            '{{content}} ' => $HTML,
            # Url del botón
            '{{btn-href}}' => $config['build']['url'],
            # Texto del boton
            '{{btn-name}}' => 'Ir a ' . $config['build']['name'],
            # Copyright
            '{{copyright}}' => '&copy; '.date('Y') .' <a href="'.$config['build']['url'].'">'.$config['build']['name'].'</a> - Todos los derechos reservados.'
        ),0);
    }


    /**
     * Valida las acciones realizables con las carteras correspondientes a compras, ventas e intercambios
     */
    private function checkTransaction($monto = 0){

        #Si el tipo de compra NO es un intercambio
        if( $this->tipo != 3  ){

            #Valida la existencia de la moneda en la cartera del comprador/vendedor
            $existencia_cartera = $this->db->select('id_usuario_moneda','user_moneda',null,"id_usuario='$this->id_usuario' and 
            codigo_moneda='$this->codigo_moneda'");

            #1 compra, 2 venta
            if( $this->tipo == 1  ){

                #Si existe la moneda en la cartera es porque el usuario ya la compró                                                                             
                if($existencia_cartera != false ) {
                    throw new ModelsException('El usuario ya posee la moneda.');
                }

                if(!(Helper\Functions::emp($this->id_comercio))){

                    # Se verifica si el comercio posee la moneda
                    $data_comercio = $this->db->select('codigo','afiliado_moneda',null,"codigo = $this->codigo_moneda");

                    if (false === $data_comercio){
                        throw new ModelsException('El comercio no posee esta moneda.');
                    }

                    # Si la posee, entonces se borra de afiliado_moneda
                    $this->db->delete('afiliado_moneda',"codigo = $this->codigo_moneda AND id_comercio_afiliado = $this->id_comercio");

                }

                #Agrega la cartera
                $this->db->insert('user_moneda',array(
                    'id_usuario' => $this->id_usuario,
                    'codigo_moneda' => $this->codigo_moneda
                ));
                                                                                            
            }else
            if( $this->tipo == 2 ){

                #Si no existe la moneda es porque el usuario no la tiene                                                                        
                if($existencia_cartera == false ) {
                    throw new ModelsException('El usuario no posee la moneda.');
                }

                if(!(Helper\Functions::emp($this->id_comercio))){

                    # Se verifica si el comercio posee la moneda
                    $data_comercio = $this->db->select('codigo','afiliado_moneda',null,"codigo = $this->codigo_moneda");
                    if (false !== $data_comercio){
                        throw new ModelsException('El comercio ya posee esta moneda.');
                    }

                    # Si no posee la moneda, entonces se inserta en afiliado_moneda
                    $this->db->insert('afiliado_moneda',array(
                        'id_comercio_afiliado'=>$this->id_comercio,
                        'codigo'=>$this->codigo_moneda,
                        'monto'=>$monto,
                        'fecha'=> time()
                    ));                  
                }

                #Borra la cartera
                $id_cartera = $existencia_cartera[0]['id_usuario_moneda'];

                $this->db->delete('user_moneda',"id_usuario_moneda=$id_cartera");

            }

                                                                                          
        }else

        #Si el tipo de compra SI es un intercambio
        if($this->tipo == 3){

            #
            $c1 = $this->db->select('id_usuario_moneda','user_moneda',null,"id_usuario='$this->id_usuario' and codigo_moneda='$this->codigo_moneda'");
            $c12 = $this->db->select('id_usuario_moneda','user_moneda',null,"id_usuario='$this->id_usuario' and codigo_moneda='$this->codigo_moneda2'");

            $c2 = $this->db->select('id_usuario_moneda','user_moneda',null,"id_usuario='$this->id_usuario2' and codigo_moneda='$this->codigo_moneda2'");
            $c21 = $this->db->select('id_usuario_moneda','user_moneda',null,"id_usuario='$this->id_usuario2' and codigo_moneda='$this->codigo_moneda'");

            if( $c1 == false or $c2 == false){
                throw new ModelsException('Los usuarios deben poseer la moneda a cambiar.');
            }else
            if($c12 != false or $c21 != false){
                throw new ModelsException('Los usuarios no pueden recibir monedas que ya poseen.');
            }else{

                $this->db->insert('user_moneda',array(
                    'id_usuario' => $this->id_usuario,
                    'codigo_moneda' => $this->codigo_moneda2
                ));

                $this->db->insert('user_moneda',array(
                    'id_usuario' => $this->id_usuario2,
                    'codigo_moneda' => $this->codigo_moneda
                ));

                $c1 = $c1[0]['id_usuario_moneda'];
                $c2 = $c2[0]['id_usuario_moneda'];

                $this->db->delete('user_moneda',"id_usuario_moneda='$c1'",'1');
                $this->db->delete('user_moneda',"id_usuario_moneda='$c2'",'1');
            }


        }


    }


    /**
     * Calcula el precio de la moneda de acuerdo a su composicion
     * 
     * @param id_moneda :  id de la moneda a calcular precio
     */
    public function calculatePrice(int $id_moneda) : int {

        $data_moneda = $this->db->select('composicion,peso','moneda',null,'codigo = '. $id_moneda);

        $composicion = $data_moneda[0]['composicion'] == 'oro' ? 'gold' : 'silver';
        
        #Url de iragold
        $url = 'https://goldiraguide.org/wp-admin/admin-ajax.php';

        #Opciones de la api
        $opt = array(
            CURLOPT_POST =>true,
            CURLOPT_RETURNTRANSFER =>true,
            CURLOPT_POSTFIELDS =>['action' => 'getMetalPrice', 'api_key' => 'anonymous'],
            CURLOPT_URL => $url
        );

        #Se hace la llamada
        $ch = curl_init();
        curl_setopt_array($ch,$opt);
        $result = curl_exec($ch);     
        curl_close($ch);

        $result = json_decode($result, true);
        $data = $result['buttonFrame'][$composicion]['1m']['data'];
        #Se calcula el valor de la moneda
        $precio_dolares = $data_moneda[0]['peso'] * ($data[29]/28.3495);

        return $precio_dolares;

    }


    /**
     * Revisa errores en el formulario
     * 
     * @return exception
    */ 
    private function errors(bool $edit = false){
        global $http;

        $this->id_usuario = $http->request->get('id_usuario');
        $this->codigo_moneda = $http->request->get('codigo');     
        $this->tipo = $http->request->get('tipo');

        $this->id_sucursal = $http->request->get('id_sucursal');
        $this->id_comercio = $http->request->get('id_comercio');

        $this->id_usuario2 = $http->request->get('id_usuario2');
        $this->codigo_moneda2 = $http->request->get('codigo2');


        # Verificar que no están vacíos
        if (Helper\Functions::e($this->id_usuario,$this->codigo_moneda,$this->tipo)) {
            throw new ModelsException('Debe seleccionar todos los elementos.');
        }

        if( ($this->tipo != 3) && (Helper\Functions::emp($this->id_sucursal)) && (Helper\Functions::emp($this->id_comercio)) ){
            throw new ModelsException('Debe seleccionar todos los elementos.');
        }

        #Se verifica que solo se elija una sucursal o un comercio
        if(!(Helper\Functions::emp($this->id_sucursal)) && !(Helper\Functions::emp($this->id_comercio))){
            throw new ModelsException('Solo puede seleccionar una sucursal o un comercio.');
        }

    }


    /**
     * Agrega una transaccion
     * 
     * @param qr :  si qr es distinto de cero entonces se esta realizando una transaccion por medio de escaneo de codigos qr
     * 
     * 
     * @return array
    */ 
    public function add(int $qr = 0) : array {

        try {

       #Revisa errores del formulario
        $this->errors();

        $qr_moneda_emision = $this->codigo_moneda;

        #Trae el id del usuario emisor de la transaccion
        if($qr == 1 or $qr == 2){
            $this->id_usuario = $this->db->select('id_user','users',null,"email='$this->id_usuario'")[0]["id_user"];
            
        }

        #Trae el id del usuario que inicio la transacción tipo intercambio (el id del receptor)
        if($qr == 2){
            $this->id_usuario2 = $this->db->select('id_usuario','transaccion_en_espera',null,"codigo_qr_moneda='$this->codigo_moneda'")[0]["id_usuario"];
        }

        #Extrae el id de la moneda principal del codigo qr
        if($qr == 1 or $qr == 2){
            $this->codigo_moneda =  $this->db->select("codigo","moneda",null,"qr_alfanumerico='$this->codigo_moneda'")[0]["codigo"];
            if($this->codigo_moneda == false ){ throw new ModelsException('Código de moneda inválido.');}
         }
         
        $precio_moneda1 = $this->calculatePrice($this->codigo_moneda); 
        $precio_moneda2 = null; 


        #Si es un intercambio
        if ( !(Helper\Functions::e($this->codigo_moneda2)) ) {

            #Extrae el id de la moneda secundaria del codigo qr
            if($qr == 2){
                $this->codigo_moneda2 = $this->db->select("codigo","moneda",null,"qr_alfanumerico='$this->codigo_moneda2'")[0]["codigo"];
                if($this->codigo_moneda2 == false ){ throw new ModelsException('Código de moneda inválido.');}

                #Valida la existencia de una transaccion en espera con la moneda escaneada
                $c = $this->db->select("codigo_qr_moneda","transaccion_en_espera",null,"codigo_qr_moneda='$this->codigo_moneda2'");
                if ($c != false) {
                    throw new ModelsException('Ya se está realizando una transacción con la moneda.');
                }

             }

            $precio_moneda2 = $this->calculatePrice($this->codigo_moneda2); 

        }   

        #Almacena los datos para realizar la transaccion
        $u = array(
            'id_usuario' => $this->id_usuario,
            'codigo_moneda' => $this->codigo_moneda,
            'precio_moneda1' => $precio_moneda1,
            'tipo' => $this->tipo,
            'id_usuario2' => $this->id_usuario2,
            'codigo_moneda2' => $this->codigo_moneda2,
            'precio_moneda2' => $precio_moneda2,
            'fecha' => time()
        );

        # Se agrega el id de la sucursal o del comercio
        $key = (Helper\Functions::emp($this->id_sucursal))?'id_comercio_afiliado':'id_sucursal';
        $u[$key] = (Helper\Functions::emp($this->id_sucursal))?$this->id_comercio:$this->id_sucursal;


        #Array con datos validos para el update
        $data = array();

        #Valida que los datos no esten vacios y los inserta en el array "data"
        foreach ($u as $key=>$val) {
            if(NULL !== $u[$key] && !Functions::emp($u[$key])){
                $data[$key] = $u[$key];
            }
        }

        #Valida datos de las monedas con respecto a los usuarios
        $this->checkTransaction($precio_moneda1);

        # Crea una transaccion
        $id_transaccion =  $this->db->insert('transaccion',$data);

        #En caso de ser un intercambio vía qr se envia un correo a los dos usuarios y se elimina la transaccion es espera
        if($qr == 2){
            $this->db->delete('transaccion_en_espera',"codigo_qr_moneda='$qr_moneda_emision'");

            #Mandar correo a los dos usuarios
        }


        #En caso de ser una transacción via código qr se envía un email de confirmación al usuario
        if($qr == 1){

            #Valída el tipo de transacción
            $tipo_transaccion = $this->tipo == 1 ? 'compra' : 'venta';

            #Solicíta y guarda datos a usar del usuario
            $datos_usuario = $this->db->select('primer_nombre, primer_apellido, email','users',null,"id_user='$this->id_usuario'");
            $nombre =  $datos_usuario[0]["primer_nombre"];
            $apellido =  $datos_usuario[0]["primer_apellido"];
            $email = $datos_usuario[0]["email"];

            #Envía el email
            $this->sendSuccesMail('Enhorabuena! ' . $nombre . ' ' . $apellido . ', ha realizado la ' . $tipo_transaccion . ' de la moneda ' . $this->codigo_moneda .
                '<br />
                <br />', $email,$nombre,$apellido);

        }

        

            return array('success' => 1, 'message' => 'Transacción creada con éxito!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }

    /**
     * Verificar errores en el intercambio con comercios
     * 
     * @param $data: Array con los datos del intercambio
     */
    private function errorsComercios($data){

        #Valida si los campos estan vacios
        if (!array_key_exists('id_usuario',$data) || Functions::emp($data['id_usuario'])) {
            throw new ModelsException('Debe seleccionar un usuario');
        }

        if (!array_key_exists('codigo',$data) || Functions::emp($data['codigo'])) {
            throw new ModelsException('Debe seleccionar una moneda');
        }

        if (!array_key_exists('id_comercio',$data) || Functions::emp($data['id_comercio'])) {
            throw new ModelsException('Debe seleccionar un comercio');
        }

        #Valida la existencia de la moneda en la cartera del comprador/vendedor
        $existencia_cartera = $this->db->select('id_usuario_moneda','user_moneda',null,"id_usuario=".$data['id_usuario']." and 
        codigo_moneda=".$data['codigo']);

        #Si no existe la moneda es porque el usuario no la tiene                                                                        
        if($existencia_cartera == false ) {
            throw new ModelsException('El usuario no posee la moneda.');
        }

        # Se verifica si el comercio posee la moneda
        $data_comercio = $this->db->select('codigo','afiliado_moneda',null,"codigo = ".$data['codigo']);
        if (false !== $data_comercio){
            throw new ModelsException('El comercio ya posee esta moneda.');
        }
    }

    /**
     * Funcion de intercambio con comercios afiliados
     * 
     */
    public function intercambiosAfiliados(){
        try{
            global $http;
            $data = $http->request->all();

            if(array_key_exists('codigo_qr',$data)){
                $moneda = $this->db->select('codigo','moneda',null,"qr_alfanumerico = '".$data['codigo_qr']."'");
                $data['codigo'] = $moneda[0]['codigo'];
            }

            $this->errorsComercios($data);

            $monto = $this->calculatePrice($data['codigo']);
            $fecha = time();

            # Se inserta en la transaccion
            $this->db->insert('transaccion',array(
                'fecha'=>$fecha,
                'tipo'=>4,
                'codigo_moneda'=>$data['codigo'],
                'id_comercio_afiliado'=>$data['id_comercio'],
                'id_usuario'=>$data['id_usuario'],
                'precio_moneda1'=> $monto
            ));

            # Si no posee la moneda, entonces se inserta en afiliado_moneda
            $this->db->insert('afiliado_moneda',array(
                'id_comercio_afiliado'=>$data['id_comercio'],
                'codigo'=>$data['codigo'],
                'monto'=>$monto,
                'fecha'=>$fecha
            )); 

            $this->db->delete('user_moneda','id_usuario='.$data['id_usuario'].' AND codigo_moneda='.$data['codigo']);
            return array('success' => 1, 'message' => 'Transaccion creada con exito');
        }
        catch (ModelsException $e){
            return array('success' => 0, 'message' => $e->getMessage());
        }
    } 

    /**
     * Trae los intercambios con comercios
     */
    public function getIntercambiosAfiliados(){
        $where = 'comercio_afiliado.id_comercio_afiliado = transaccion.id_comercio_afiliado AND users.id_user = transaccion.id_usuario AND transaccion.tipo = 4';
        $result = $this->db->select('DISTINCT users.primer_nombre, users.primer_apellido, users.id_user, comercio_afiliado.nombre, comercio_afiliado.id_comercio_afiliado','transaccion, users, comercio_afiliado',null,$where);
        return $result;
    }

    /**
     * Trae los intercambios con comercios de un user especifico
     */
    public function getIntercambiosUsers($id_user,$id_comercio){
        $where = "id_comercio_afiliado = $id_comercio AND id_usuario = $id_user AND tipo = 4";

        $intercambios = $this->db->select('codigo_moneda AS codigo, precio_moneda1 AS monto, fecha','transaccion',null,$where);

        $total = $this->db->select('IFNULL(SUM(precio_moneda1),0) AS total','transaccion',null,$where);
        return array(
            'intercambios' => $intercambios,
            'total' => $total
        );
    }

    /**
     * Realiza el inicio de un intercambio por medio de la aplicacion escaneando los codigos qr
     */
    public function receptorQr() {
        global $http;
        try {

        $email = $http->request->get('email');        
        $codigo_moneda = $http->request->get('codigo'); 
        
        if (Helper\Functions::e($email,$codigo_moneda)) {
            throw new ModelsException('Los datos no son suficientes para realizar el intercambio.');
        }

        #Valida la existencia de una transaccion en espera con la moneda escaneada
        $c = $this->db->select("codigo_qr_moneda","transaccion_en_espera",null,"codigo_qr_moneda='$codigo_moneda'");
        if ($c != false) {
            throw new ModelsException('Ya se está realizando una transacción con la moneda.');
        }

        #Trae los datos del usuario que escaneo la moneda
        $usuario_receptor = $this->db->select("id_user,primer_nombre,primer_apellido","users",null,"email='$email'");
        $id_moneda = $this->db->select("codigo","moneda",null,"qr_alfanumerico='$codigo_moneda'")[0]["codigo"];


        #Valida que el usuario no tenga la moneda que quiere recibir en el intercambio
        $inner = "INNER JOIN users u ON u.id_user = user_moneda.id_usuario";
        $usuario_emisor = $this->db->select("u.id_user,u.primer_nombre,u.primer_apellido,u.email","user_moneda",$inner,"user_moneda.codigo_moneda='$id_moneda'");

        if($usuario_emisor[0]["id_user"] == $usuario_receptor[0]["id_user"]){
            throw new ModelsException('No puedes recibir por intercambio una moneda que ya posees.');
        }

        #Guarda en la db el id del usuario que escaneo la moneda y el id de la moneda
        $data = array(
            'id_usuario' => $usuario_receptor[0]["id_user"],
            'codigo_qr_moneda' => $codigo_moneda
        );

        #Crea la transacción en espera
        $id_transaccion_en_espera =  $this->db->insert('transaccion_en_espera',$data);


        #Envía el email
        $this->sendSuccesMail(
        "Hola ".$usuario_emisor[0]["primer_nombre"]. ' ' .$usuario_emisor[0]["primer_apellido"].
        " para realizar el intercambio con ".$usuario_receptor[0]["primer_nombre"]. ' ' .$usuario_receptor[0]["primer_apellido"].
        " debe introducir el siguiente código y luego escanear su moneda".
        " Codigo :".$codigo_moneda. 
        "<br />
         <br />", (string)$usuario_emisor[0]["email"], (string)$usuario_emisor[0]["primer_nombre"], (string)$usuario_emisor[0]["primer_apellido"]);

         return array('success' => 1, 'message' => 'Correo enviado al dueño de la moneda!');
        } catch(ModelsException $e) {
         return array('success' => 0, 'message' => $e->getMessage());
        }
    }

    /**
     * Obtiene elementos de transaccion según el tipo
     *    
     * @param tipo : int, tipo de transacción
     * @return false|array con información de los usuarios
     */  
    public function getTransacciones(int $tipo = 0) {

        $inner = "INNER JOIN users u1 ON u1.id_user=transaccion.id_usuario
                  INNER JOIN moneda m1 ON m1.codigo=transaccion.codigo_moneda 
                  LEFT JOIN sucursal s ON s.id_sucursal=transaccion.id_sucursal
                  ";

        if($tipo == 1){
            return $this->db->select('transaccion.id_transaccion,transaccion.fecha,transaccion.precio_moneda1,
                                        u1.primer_nombre,u1.primer_apellido,u1.id_user,m1.codigo,
                                        s.nombre as nombre_sucursal','transaccion',$inner,'transaccion.tipo=1');
        }else 
        if($tipo == 2){
            return $this->db->select('transaccion.id_transaccion,transaccion.fecha,transaccion.precio_moneda1,
                                        u1.primer_nombre,u1.primer_apellido,u1.id_user,m1.codigo,
                                        s.nombre as nombre_sucursal','transaccion',$inner,'transaccion.tipo=2');
        }else
        if($tipo == 3){
            $inner2 = "INNER JOIN users u2 ON u2.id_user=transaccion.id_usuario2 
                       INNER JOIN moneda m2 ON m2.codigo=transaccion.codigo_moneda2";

            $inner = $inner . $inner2;         
            return $this->db->select('transaccion.id_transaccion,transaccion.fecha,transaccion.precio_moneda1,transaccion.precio_moneda2,
                                        u1.primer_nombre as pn1,u1.primer_apellido as pa1,u1.id_user as iu1,m1.codigo as c1,
                                        u2.primer_nombre as pn2,u2.primer_apellido as pa2,u2.id_user as iu2,m2.codigo as c2'
                                        ,'transaccion',$inner,'transaccion.tipo=3');
        }

        return $this->db->select('transaccion.*','transaccion',$inner);
    }

    #Sii el usuario es del tipo 2 traer moneda 2
    public function getByUser(int $id_user){
        $inner = "INNER JOIN moneda m1 ON m1.codigo=transaccion.codigo_moneda
                  LEFT  JOIN moneda m2 ON m2.codigo=transaccion.codigo_moneda2
                  LEFT JOIN sucursal s ON s.id_sucursal=transaccion.id_sucursal";

        return $this->db->select('transaccion.fecha,transaccion.tipo,transaccion.id_usuario,s.nombre,m1.codigo as m1,m2.codigo as m2',
                                 'transaccion',$inner,"transaccion.id_usuario='$id_user' OR transaccion.id_usuario2='$id_user'");

    }


    /**
     * __construct()
    */
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
        $this->startDBConexion();
    }
}