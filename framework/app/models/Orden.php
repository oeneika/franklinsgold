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

/**
 * Modelo Comprayventa
 */
class Orden extends Models implements IModels {
    use DBModel;

    private $id_usuario;
    private $tipo_gramo;
    private $cantidad;
    private $id_sucursal;
    private $tipo_orden;
    private $id_moneda;
    private $file;

    /**
     * Revisa errores en el formulario
     * 
     * @return exception
    */ 
    private function errors(bool $edit = false){
        global $http;

        # Obtener los datos $_FILES los cuales contienen las imagenes cargadas
        $this->file = $_FILES;
       // dump($this->file);
       
        #Obtener los datos $_POST
        #Usada en caso de ser una compra/venta vía movil o tienda
        $email = $http->request->get('email');
        if ( !Helper\Functions::emp($email) and $email!=null) {
            $email = $this->db->scape($email);

            $this->id_usuario = $this->db->select("id_user","users",null,"email='$email'")[0]["id_user"];
            
        }else{
            $this->id_usuario = ((new Model\Users)->getOwnerUser())["id_user"];
        }

        $this->tipo_gramo = $http->request->get('tipo_gramo');     
        $this->cantidad = $http->request->get('cantidad');
        $this->tipo_orden = $http->request->get('tipo_orden');
        $this->id_moneda = $http->request->get('id_moneda');

        #Para validar el mínimo y el máximo diario
        $cantidad_bolivar_soberano = $http->request->get('cantidad_bolivar_soberano');
        $monto_dolares = $http->request->get('monto_dolares');

        # Verificar que no están vacíos
        if (Helper\Functions::e($this->id_usuario,$this->tipo_gramo,$this->cantidad/*,$this->id_sucursal*/,$this->tipo_orden)) {
            throw new ModelsException('Debe seleccionar todos los elementos.');
        }

        if($this->cantidad<=0){
            throw new ModelsException('La cantidad de gramos debe ser mayor a cero.');
        }

        if( $this->tipo_gramo != "oro" and $this->tipo_gramo != "plata" ){
            throw new ModelsException('Tipo de gramo inválido.');
        }

        if( $this->tipo_orden != 1 and $this->tipo_orden != 2 and $this->tipo_orden != 3 ){
            throw new ModelsException('Tipo de orden inválida.');
        }

        #En caso de ser una compra/venta via web o app movil, se usa isset porque en js se usó un metodo disinto para serializar(caso particular)
        if( $this->tipo_gramo === 'oro' and $cantidad_bolivar_soberano<20000 and $monto_dolares!=null and $this->tipo_orden!==3){
            throw new ModelsException('Las compras o ventas de oro son a partir 20.000 BsS.');
        }

        if( $this->tipo_gramo === 'plata' and ($cantidad_bolivar_soberano<1 or $cantidad_bolivar_soberano>20000) and $monto_dolares!=null and $this->tipo_orden!==3 ){
            throw new ModelsException('Las compras o ventas de plata deben ser entre 1 y 20.000 BsS.');
        }

        #Si no es un intercambio o un compra desde tienda procede a validar el dinero movido en el día
        if ( $this->tipo_orden!== 3 and $monto_dolares == null) {

        #Valido según el rango del usuario si es posible hacer una transacción mas
        $rango = $this->db->select("tipo_cliente","users",null,"id_user='$this->id_usuario'")[0]["tipo_cliente"];
        $total_movido_hoy = $this->getDailyMoneyByUser($this->id_usuario) + ($monto_dolares == null ? 0 : $monto_dolares);
        
            #Valida que la compra no exceda el monto diario permitido según el rango
            if($total_movido_hoy > $this->getDailyMoneyByRango($rango)  ){
                throw new ModelsException('Ha excedido el límite diario de dinero movido.');
            }
            
        }

        #Si se subió una foto valida que se haya hecho bien y el formato
        if (array_key_exists('foto_transferencia',$this->file)) {
            
            if ($this->file["foto_transferencia"]["error"] != 0) {
                throw new ModelsException('Hubo un error cargando la imagen de la transferencia, intente de nuevo.');
            }

            if ( !strpos($this->file["foto_transferencia"]["type"], "jpeg") and !strpos($this->file["foto_transferencia"]["type"], "jpg") and
            !strpos($this->file["foto_transferencia"]["type"], "png") ){
                throw new ModelsException('La transferencia debe ser una imagen.');
            }

        }

        #Trae la cantidad en cartera para validar si existe la posibilidad de vender/intercambiar
        if($this->tipo_orden == 2 or $this->tipo_orden == 3){
           
            $cantidad_en_cartera = $this->db->select("cantidad","user_gramo",null,"id_usuario='$this->id_usuario' and tipo_gramo='$this->tipo_gramo'");

            if($cantidad_en_cartera != false){
                $cantidad_en_cartera = $cantidad_en_cartera[0]["cantidad"];

                if($cantidad_en_cartera < $this->cantidad){
                    throw new ModelsException('No puede vender/intercambiar una cantidad que no posee.');
                }  

            }else{
                throw new ModelsException('No puede vender/intercambiar gramos que no posee.');
            }

            #Si es un intercambio exige la moneda
            if($this->tipo_orden == 3) {    

                if(Helper\Functions::emp($this->id_moneda) or $this->id_moneda==null){
                    throw new ModelsException('Debe seleccionar la moneda.');
                }
                
                $moneda = $this->db->select("peso,composicion","moneda",null,"codigo='$this->id_moneda'");
                $peso = $moneda[0]["peso"];
                $composicion = $moneda[0]["composicion"];

                if($this->tipo_gramo !== $composicion){
                    throw new ModelsException('Las composiciones no coinciden.');
                }

                if($peso != $this->cantidad){
                    throw new ModelsException('Los gramos de oro a intercambiar no coinciden con el peso(gramos) de la moneda.');
                }

            }

        }

    }


    /**
     * Crea una orden de compra/venta
     * 
     * @return array
    */ 
    public function createOrden() : array {
        try {

            #Valida los posibles errores
            $this->errors();
            
            $d = new Model\Divisa();

            #Si es oro trae el precio del oro 
            if($this->tipo_gramo === 'oro'){

                #Si es compra trae el precio de compra
                if($this->tipo_orden==1){
                    $precio = $d->getDivisas("precio_dolares","nombre_divisa='Oro Franklin'")[0]["precio_dolares"];
                }else{
                    $precio = $d->getDivisas("precio_dolares_venta","nombre_divisa='Oro Franklin'")[0]["precio_dolares_venta"];
                }             

            }else{

                #Si es compra trae el precio de compra
                if($this->tipo_orden==1){
                    $precio = $d->getDivisas("precio_dolares","nombre_divisa='Plata Franklin'")[0]["precio_dolares"];
                }else{
                    $precio = $d->getDivisas("precio_dolares_venta","nombre_divisa='Plata Franklin'")[0]["precio_dolares_venta"];
                }     

            }

            $orden = array(
                'id_usuario' => $this->id_usuario,
                'tipo_gramo' => $this->tipo_gramo,
                'cantidad' => $this->cantidad,
                'precio' => $precio,
                /*'id_sucursal' => $this->id_sucursal,*/ //Forzando una sucursal de momento
                'tipo_orden' => $this->tipo_orden,
                'fecha' => time(),
                'estado' => 1
            );
            
            if(!Helper\Functions::emp($this->id_moneda) or $this->id_moneda!=null){
                $orden["codigo_moneda"] = $this->id_moneda;
            }
        
           #Crea una orden
           $id_orden =  $this->db->insert('orden',$orden);

           #Usada para definir correctamente la dirección a guardar la imagen
           $path = "../";

           #Si se cargó la foto de la transferencia se guarda y se actualiza la db
           $dir_transferencia=null;
           if (array_key_exists('foto_transferencia',$this->file)) {               
               $dir_transferencia = "views/img/transferencias/bancaria".$id_orden.".png";
                          
               $tmp_name = $this->file["foto_transferencia"]["tmp_name"];
               // basename() puede evitar ataques de denegación de sistema de ficheros;
               // podría ser apropiada más validación/saneamiento del nombre del fichero
               $name = basename($this->file["foto_transferencia"]["name"]);
               move_uploaded_file($tmp_name, "$path"."$dir_transferencia");


             #Se actualiza la db con la ruta de los documentos
             $this->db->update('orden',array(
                'foto_transferencia'=> $dir_transferencia
            ), "id_orden = '$id_orden'");
                   
           }
                    
            return array('success' => 1, 'message' => 'Orden creada con exito!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }

    /**
     * Da confirmaciones a las ordenes antes de ser concretada
     */
    public function confirmOrden(){
        Global $config;

        #Trae la orden, valida su existencia y la concreta
        $orden = $this->db->select("estado,id_usuario","orden",null,"id_orden='$this->id'");

        #Si la orden no existe no hace ninguna confirmación
        if($orden == false){
            Functions::redir($config['build']['url'] . 'ordenadmin/&success=false');
        }

        $id_user = $orden[0]["id_usuario"];

        #Si el usuario logeado es un vendedor y la orden esta en estado 1 da la primera confirmación
        if($orden[0]["estado"] == 1 and ((new Model\Users)->getOwnerUser())["tipo"] == 1){

            $this->db->update('orden',array(
                'estado'=>2
            ),"id_orden = $this->id");

            #Trae los datos del usuario de la orden
            $user = $this->db->select("primer_nombre,primer_apellido,email","users",null,"id_user='$id_user'");

            #Envía correo informativo sobre la orden al usuario
            (new Model\Transaccion)->sendSuccesMail('Estimado ' . $user[0]["primer_nombre"] . ' ' . $user[0]["primer_apellido"] . 
            ', le informamos que su orden ha sido confirmada por un vendedor de Franklins Gold, la misma se hará efectiva con la '.
            'confirmación de un supervisor y posteriormente la confirmación de un administrador, le estaremos informando dicha trama.'.
             '<br /> 
             <br />', $user[0]["email"],$user[0]["primer_nombre"],$user[0]["primer_apellido"]);

        }

         #Si el usuario logeado es un supervisor y la orden esta en estado 2 da la segunda confirmación
        if($orden[0]["estado"] == 2 and ((new Model\Users)->getOwnerUser())["tipo"] == 3){

            $this->db->update('orden',array(
                'estado'=>3
            ),"id_orden = $this->id");

            #Trae los datos del usuario de la orden
            $user = $this->db->select("primer_nombre,primer_apellido,email","users",null,"id_user='$id_user'");

            #Envía correo informativo sobre la orden al usuario
            (new Model\Transaccion)->sendSuccesMail('Estimado ' . $user[0]["primer_nombre"] . ' ' . $user[0]["primer_apellido"] . 
            ', le informamos que su orden ha sido confirmada por un supervisor de Franklins Gold, la misma se hará efectiva con la '.
            'confirmación de un administrador, le estaremos informando dicha trama.'.
             '<br /> 
             <br />', $user[0]["email"],$user[0]["primer_nombre"],$user[0]["primer_apellido"]);

        }

        Functions::redir($config['build']['url'] . 'ordenadmin/&success=true');

    }

    /**
     * Concreta una orden de compra/venta
     * 
     * @return array
    */ 
    public function specifyOrden(){
       
        Global $config;

            #Trae la orden, valida su existencia y la concreta
            $orden = $this->db->select("id_usuario,tipo_gramo,cantidad,tipo_orden,codigo_moneda","orden",null,"id_orden='$this->id'");

            #Si la orden no existe o el usuario no es un administrador no la concreta
            if($orden == false or (((new Model\Users)->getOwnerUser())["tipo"] != 0)){
                Functions::redir($config['build']['url'] . 'ordenadmin/&success=false');
            }


            #Trae la cartera si existe
            $id_usuario = $orden[0]["id_usuario"];
            $tipo_gramo = $orden[0]["tipo_gramo"];
            $cantidad_orden = $orden[0]["cantidad"];
            $cartera = $this->db->select("id_usuario_gramo,cantidad","user_gramo",null,"id_usuario='$id_usuario' and tipo_gramo='$tipo_gramo'");

            #Trae los datos del usuario de la orden
            $user = $this->db->select("primer_nombre,primer_apellido,email","users",null,"id_user='$id_usuario'");


            #Si la orden es una compra inserta o actualiza
            if($orden[0]["tipo_orden"] == 1){

                if( $cartera == false ){
               
                    $this->db->insert('user_gramo',array(
                        'id_usuario' => $id_usuario,
                        'tipo_gramo' => $tipo_gramo,
                        'cantidad' => $cantidad_orden
                    ));

                }else{

                    $id_cartera = $cartera[0]["id_usuario_gramo"];
                    $cantidad = $cartera[0]["cantidad"];

                    #Actualiza la cartera del usuario
                    $this->db->update('user_gramo',array(
                        'cantidad'=> $cantidad + $cantidad_orden
                    ),"id_usuario_gramo = '$id_cartera'");    

                }

                $this->db->update('orden',array(
                    'estado'=>4
                ),"id_orden = $this->id");


                #Envía correo informativo sobre la orden al usuario
                (new Model\Transaccion)->sendSuccesMail('Estimado ' . $user[0]["primer_nombre"] . ' ' . $user[0]["primer_apellido"] . 
                ', le informamos que su orden ha sido confirmada totalmente, en este momento su transacción ha sido satisfactoria.'.
                '<br /> 
                <br />', $user[0]["email"],$user[0]["primer_nombre"],$user[0]["primer_apellido"]);

                Functions::redir($config['build']['url'] . 'ordenadmin/&success=true');

            }else{

                #En el caso de que la cartera no exista implica que el usuario ya no tiene gramos para vender/intermcabiar
                if( $cartera == false ){
               
                    Functions::redir($config['build']['url'] . 'ordenadmin/&success=false');

                }else{
               
                    $id_cartera = $cartera[0]["id_usuario_gramo"];
                    $cantidad = $cartera[0]["cantidad"];

                    if( $cantidad >= $cantidad_orden ){

                        #Actualiza la cartera del usuario
                        $this->db->update('user_gramo',array(
                            'cantidad'=> $cantidad - $cantidad_orden
                        ),"id_usuario_gramo = '$id_cartera'");  

                    #Si es un intercambio entonces añade la moneda a la cartera del usuario   
                    if($orden[0]["tipo_orden"] == 3){

                        #Borra la cartera anterior
                        $id_moneda = $orden[0]["codigo_moneda"];
                        $this->db->delete('user_moneda',"codigo_moneda=$id_moneda");
                        

                        #Agrega la cartera
                        $this->db->insert('user_moneda',array(
                            'id_usuario' => $id_usuario,
                            'codigo_moneda' => $id_moneda
                        ));                       

                    }

                        $this->db->update('orden',array(
                            'estado'=>4
                        ),"id_orden = $this->id");


                        #Envía correo informativo sobre la orden al usuario
                        (new Model\Transaccion)->sendSuccesMail('Estimado ' . $user[0]["primer_nombre"] . ' ' . $user[0]["primer_apellido"] . 
                        ', le informamos que su orden ha sido confirmada totalmente, en este momento su transacción ha sido satisfactoria.'.
                        '<br /> 
                        <br />', $user[0]["email"],$user[0]["primer_nombre"],$user[0]["primer_apellido"]);

                        Functions::redir($config['build']['url'] . 'ordenadmin/&success=true');

                        
                    }else{
                        #El usuario ya no tiene gramos para vender/intermcabiar
                        Functions::redir($config['build']['url'] . 'ordenadmin/&success=false');

                    }

                }
                
            }       
    }


    /**
     * Crea una orden en espera
     */
    public function createOrdenEnEspera(){

        try {
        global $http;

        #Obtener los datos $_POST
        $id_vendedor_owner = ((new Model\Users)->getOwnerUser())["id_user"];
        $id_cliente = $http->request->get('id_cliente'); 
        $tipo_gramo = $http->request->get('tipo_gramo');     
        $cantidad = $http->request->get('cantidad');
        

        # Verificar que no están vacíos
        if (Helper\Functions::e($id_cliente,$tipo_gramo,$cantidad)) {
            throw new ModelsException('Debe seleccionar todos los elementos.');
        }

        if($cantidad<=0){
            throw new ModelsException('La cantidad de gramos debe ser mayor a cero.');
        }

        if( $tipo_gramo != "oro" and $tipo_gramo != "plata" ){
            throw new ModelsException('Tipo de gramo inválido.');
        }

        
        #Verifica si el cliente posee lo suficiente en gramos para pagar
        $cantidad_en_cartera = $this->db->select("cantidad","user_gramo",null,"id_usuario='$id_cliente' and tipo_gramo='$tipo_gramo'");

        if($cantidad_en_cartera != false){
            $cantidad_en_cartera = $cantidad_en_cartera[0]["cantidad"];

            if($cantidad_en_cartera < $cantidad){
                throw new ModelsException('El cliente no posee lo suficiente para pagar.');
            }  

        }else{
            throw new ModelsException('El cliente no posee lo suficiente para pagare.');
        }

            #Token de confirmacion
            $token = substr(uniqid(chr(rand(97,122))), 0, 8);

            #Array con los datos para el insert
            $orden = array(
                'id_usuario_vendedor' => $id_vendedor_owner,
                'id_usuario_cliente' => $id_cliente,
                'tipo_gramo' => $tipo_gramo,
                'cantidad' => $cantidad,
                'codigo_confirmacion' => $token
            );
                   
           #Crea una orden en espera
           $id_orden =  $this->db->insert('orden_en_espera',$orden);

           #Trae los datos del cliente para enviarle el correo con el codigo de confirmacion
           $id_cliente = $this->db->scape($id_cliente);
           $usuario_emisor = $this->db->select("primer_nombre,primer_apellido,email","users",null,"id_user='$id_cliente'");
           
           #Envía el email para confirmar la transaccion
           (new Model\Transaccion())->sendSuccesMail('Enhorabuena! ' . $usuario_emisor[0]["primer_nombre"] . ' ' . $usuario_emisor[0]["primer_apellido"] . 
           ', para concretar su compra debe proveerle el siguiente código al vendedor : ' . $token .
            '<br /> 
            <br />', $usuario_emisor[0]["email"],$usuario_emisor[0]["primer_nombre"],$usuario_emisor[0]["primer_apellido"]);
                    
            return array('success' => 1, 'message' => 'Se ha enviado un correo con el código de confirmacíón al cliente!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }    

    }


    /**
     * Concreta una orden en espera
     */
    public function concreteOrdenEnEspera(){

        try {
            global $http;
    
            #Obtener los datos $_POST
            $codigo_confirmacion = $http->request->get('codigo_confirmacion'); 

            #Busca la orden en espera
            $orden = $this->db->select("*","orden_en_espera",null,"codigo_confirmacion='$codigo_confirmacion'");

            #Verifica si existe la orden en espera
            if($orden == false){
                throw new ModelsException('El código no corresponde a ninguna orden en espera.');
            }

            #Contiene los datos de la orden
            $id_cliente = $orden[0]["id_usuario_cliente"];
            $tipo_gramo = $orden[0]["tipo_gramo"];
            $cantidad_en_orden = $orden[0]["cantidad"];
            $id_usuario_vendedor = $orden[0]["id_usuario_vendedor"];
            $codigo_confirmacion = $orden[0]["codigo_confirmacion"];

            #Trae la cartera del cliente
            $cartera = $this->db->select("cantidad,id_usuario_gramo","user_gramo",null,"id_usuario='$id_cliente' and tipo_gramo='$tipo_gramo'");
            $id_cartera = $cartera[0]["id_usuario_gramo"];
            $cantidad_en_cartera = $cartera[0]["cantidad"];

            #Si la cantidad en la cartera es mayor o igual a la cantidad de la orden se procede
            if( $cantidad_en_cartera >= $cantidad_en_orden ){

                $d = new Model\Divisa();

                #Si es oro trae el precio del oro 
                if($tipo_gramo === 'oro'){

                    #Si es compra trae el precio de compra
                    if($this->tipo_orden==1){
                        $precio = $d->getDivisas("precio_dolares","nombre_divisa='Oro Franklin'")[0]["precio_dolares"];
                    }else{
                        $precio = $d->getDivisas("precio_dolares_venta","nombre_divisa='Oro Franklin'")[0]["precio_dolares_venta"];
                    }

                }else{

                    #Si es compra trae el precio de compra
                    if($this->tipo_orden==1){
                        $precio = $d->getDivisas("precio_dolares","nombre_divisa='Plata Franklin'")[0]["precio_dolares"];
                    }else{
                        $precio = $d->getDivisas("precio_dolares_venta","nombre_divisa='Plata Franklin'")[0]["precio_dolares_venta"];
                    }

                }

                $orden = array(
                    'id_usuario' => $id_cliente,
                    'id_vendedor' => $id_usuario_vendedor,
                    'tipo_gramo' => $tipo_gramo,
                    'cantidad' => $cantidad_en_orden,
                    'precio' => $precio,
                    'tipo_orden' => 2,
                    'fecha' => time(),
                    'estado' => 4
                );
                             
               #Crea la orden
               $this->db->insert('orden',$orden);

                #Actualiza la cartera del usuario
                $this->db->update('user_gramo',array(
                    'cantidad'=> $cantidad_en_cartera - $cantidad_en_orden
                ),"id_usuario_gramo = '$id_cartera'");  

                #Borra la orden en espera
                $this->db->delete('orden_en_espera',"codigo_confirmacion='$codigo_confirmacion'");
          
            }else{
                throw new ModelsException('El cliente no posee lo suficiente para pagar.');
            }

            return array('success' => 1, 'message' => 'Se ha concretado la orden!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }

    }


    /**
     * Trae las ordenes
     * 
     * @return array
     */
    public function get(string $select = "*",string $where="1=1",$limit=null,$extra=''){

        $inner = "INNER JOIN users u ON u.id_user = orden.id_usuario";
                //INNER JOIN sucursal s On s.id_sucursal = orden.id_sucursal

        return $this->db->select($select,"orden",$inner,$where,$limit,$extra);

    }


    /**
     * Trae las cantidades compradas o vendidas de oro o plata en cierta cantidad de tiempo
     * 
     * @return array
     */
    public function getVolumenes($days ='-31 days',$composition = "oro",$type = 1){

        $past = strtotime($days);
        $present = strtotime('now');

        return $this->db->select("SUM(cantidad) as volumen","orden",null,"tipo_orden='$type' and estado=4 and tipo_gramo='$composition' and fecha>='$past' and fecha<='$present'");

    }
    

    /**
     * Trae el total de gramos en cartera
     * 
     * @param string composicion : "oro" o "plata"
     * 
     * @param string where :  condiciones de la consulta
     * 
     * @return array
     */
    public function getTotalGramos($composicion,$where="1=1"){

        $result = $this->db->select("cantidad","user_gramo",null,"tipo_gramo='$composicion' and $where");

        if( $result == false){
            return 0;
        }

        return $result[0]["cantidad"];

    }


    /**
     * Servicio que devuelve las ultimas cinco ordenes concretadas
     */
    public function getUltTransacciones(){
        Global $http;
    
        $tipo_gramo = $this->db->scape($http->request->get('tipo_gramo'));
        $tipo = $this->db->scape($http->request->get('tipo'));
        $email = $this->db->scape($http->request->get('email'));
 
        $id_usuario = $this->db->select("id_user","users",null,"email='$email'")[0]["id_user"];
              
        $select = "orden.*,u.primer_nombre,u.primer_apellido,u.numero_cuenta";
        $where = "orden.estado=4 and orden.tipo_gramo='$tipo_gramo' and orden.tipo_orden='$tipo' and u.id_user='$id_usuario'";
    
        return $this->get($select,$where,5,"ORDER BY orden.id_orden DESC");
    }


    /**
     * Retorna la cantidad de dólares movidos en compra y venta de gramos por usuario en el día actual
     * 
     * @param int id_user :  id del usuario a traer el monto diario
     * 
     * @return int
     */
    public function getDailyMoneyByUser(int $id_user){
        
        $inicio_hoy = strtotime('today midnight');       
        $query = $this->db->select("SUM(cantidad*precio) as monto_diario","orden",null,"id_usuario='$id_user' and fecha>'$inicio_hoy' and estado=4",null,"ORDER BY id_usuario");
   
        if($query == false){
            return 0;
        }

        return $query[0]["monto_diario"];
    }

    /**
     * Retorna la cantidad de dólares que puede mover según el rango
     */
    private function getDailyMoneyByRango($rango){

        return $this->db->select("monto_diario","rango",null,"nombre_rango='$rango'")[0]["monto_diario"];


    }


    /**
     * Servicio que devuelve las ultimas cinco ordenes concretadaspor usuario
     */
    public function getOrdenesByUser(){
        Global $http;
    
        $email = $this->db->scape($http->request->get('email'));
 
        $id_usuario = $this->db->select("id_user","users",null,"email='$email'")[0]["id_user"];
              
        $select = "orden.*,u.primer_nombre,u.primer_apellido,u.numero_cuenta";
        $where = "orden.estado=4 and u.id_user='$id_usuario'";
    
        return $this->get($select,$where,5,"ORDER BY orden.id_orden DESC");
    }

     /**
     * Devuelve las ordenes que pertenecen a comercios afiliados
     * 
     *  @param int id_comercio_afiliado :  id del comercio afiliado
     *  @param int id_vendedor :  id del vendedor del comercio afiliado
     * 
     *  @return array|false
     */
    public function getOrdenesComerciosAfiliados(int $id_comercio_afiliado = 0,int $id_vendedor = 0){
        Global $http;

        $where="o.estado=4";

        if($id_comercio_afiliado != 0){
            $where = $where." and u.id_comercio_afiliado=$id_comercio_afiliado";
        }

        if($id_vendedor != 0){
            $where = $where." and u.id_user=$id_vendedor";
        }

        $inner = "INNER JOIN users u ON u.id_user=o.id_vendedor
                  INNER JOIN comercio_afiliado ca ON ca.id_comercio_afiliado=u.id_comercio_afiliado "; 
 
        return $this->db->select("o.*,ca.nombre as nombre_afiliado,ca.sucursal,u.primer_nombre,u.primer_apellido",
                                 "orden o",$inner,$where,null,"ORDER BY o.id_orden DESC");
    }


    /**
     * Servicio que devuelve la cantidad de gramos de oro/plata en posesión de un usuario
     */
    public function getGramosOroPlata(){
        Global $http;

        $tipo_gramo = $this->db->scape($http->request->get('tipo_gramo'));
        $email = $this->db->scape($http->request->get('email'));

        $id_usuario = $this->db->select("id_user","users",null,"email='$email'")[0]["id_user"];
  
        return $this->getTotalGramos($tipo_gramo,"id_usuario='$id_usuario'");         
    }



     /**
     * Elimina una orden
     * 
     * @return array
     */
    public function del(){
        global $config;

        #Trae la orden si existe
        $orden = $this->db->select("estado","orden",null,"id_orden='$this->id'");

        #Si la orden que se esta tratando de borrar no existe
        if($orden == false){
            Functions::redir($config['build']['url'] . 'ordenadmin/&success=false');
        
        #Si la orden existe entonces valida su estado   
        }else{

            #Si la orden no ha sido concretada se podra borrar
            if($orden[0]["estado"] = 1){
                $this->db->delete('orden',"id_orden=$this->id");

                Functions::redir($config['build']['url'] . 'ordenadmin/&success=true');
            }

            Functions::redir($config['build']['url'] . 'ordenadmin/&success=false');
        }

        
    }


    /**
     * __construct()
    */
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
		$this->startDBConexion();
    }
}