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
 * Modelo Transaccion
 */
class Transaccion extends Models implements IModels {
    
    use DBModel;
    private $id_usuario;
    private $id_usuario2;
    private $codigo_moneda;
    private $codigo_moneda2;
    private $id_sucursal;
    private $tipo;


    private function checkTransaction(){

        #Si el tipo de compra NO es un intercambio
        if( $this->tipo != 3  ){

            #Valida la existencia de la moneda en la cartera del comprador/vendedor
            $existencia_cartera = $this->db->select('id_usuario_moneda','user_moneda',null,"id_usuario='$this->id_usuario' and 
            codigo_moneda='$this->codigo_moneda'");

            #1 compra, 2 venta, 3 intercambio
            if( $this->tipo == 1  ){

                #Si existe la moneda en la cartera es porque el usuario ya la compró                                                                             
                if($existencia_cartera != false ) {
                    throw new ModelsException('El usuario ya ha comprado la moneda.');
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

        $monedaData = $this->db->select('composicion,peso','moneda',null,"codigo='$id_moneda'");

        $composicion = $monedaData[0]["composicion"];
        $peso = $composicion = $monedaData[0]["peso"];


        if($composicion == "oro"){
            $url = 'https://www.quandl.com/api/v3/datasets/LBMA/GOLD.json';
        }else{
            $url = 'https://www.quandl.com/api/v3/datasets/LBMA/SILVER.json';
        }


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);     
        curl_close($ch);
               
        $obj = json_decode($result);
        $dataset = $obj->{'dataset'};
        $data = $dataset->{'data'};
        //dump($data[0]);
        $precio_dolares = $peso * ($data[0][1]/28.3495); 

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

        $this->id_usuario2 = $http->request->get('id_usuario2');
        $this->codigo_moneda2 = $http->request->get('codigo2');


        # Verificar que no están vacíos
        if (Helper\Functions::e($this->id_usuario,$this->codigo_moneda,$this->tipo)) {
            throw new ModelsException('Debe seleccionar todos los elementos.');
        }

        if( ($this->tipo != 3) and (Helper\Functions::e($this->id_sucursal)) ){
            throw new ModelsException('Debe seleccionar todos los elementos.');
        }

    }


    /**
     * Agrega una transaccion
     * 
     * @return array
    */ 
    public function add() : array {

        try {

       #Revisa errores del formulario
        $this->errors();

        $precio_moneda1 = $this->calculatePrice($this->codigo_moneda); 
        $precio_moneda2 = null; 

        if ( !(Helper\Functions::e($this->codigo_moneda2)) ) {
            $precio_moneda2 = $this->calculatePrice($this->codigo_moneda2); 
        }   

        $u = array(
            'id_usuario' => $this->id_usuario,
            'codigo_moneda' => $this->codigo_moneda,
            'precio_moneda1' => $precio_moneda1,
            'tipo' => $this->tipo,
            'id_sucursal' => $this->id_sucursal,
            'id_usuario2' => $this->id_usuario2,
            'codigo_moneda2' => $this->codigo_moneda2,
            'precio_moneda2' => $precio_moneda2,
            'fecha' => time()
        );


        #Array con datos validos para el update
        $data = array();

        #Valida que los datos no esten vacios y los inserta en el array "data"
        foreach ($u as $key=>$val) {
            if(NULL !== $u[$key] && !Functions::emp($u[$key])){
                $data[$key] = $u[$key];
            }
        }

        $this->checkTransaction();

        # Crea una transaccion
        $id_transaccion =  $this->db->insert('transaccion',$data);

            return array('success' => 1, 'message' => 'Transacción creada con éxito!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }


    /**
     * Agrega una transaccion en base al escaneo de un codigo qr
     * 
     * @return array
    */ 
    public function addByQr() : array {

        try {

       #Revisa errores del formulario
        $this->errors();

        #Extrae el id de la moneda principal del codigo qr
        $this->codigo_moneda = substr($this->codigo_moneda, -1);    

        #
        $precio_moneda1 = $this->calculatePrice($this->codigo_moneda); 
        $precio_moneda2 = null; 

        #En el caso de existir un segundo codigo de moneda se realiza un intercambio
        if ( !(Helper\Functions::e($this->codigo_moneda2)) ) {

            //Extrae el id de la moneda secundaria del codigo qr
            $this->codigo_moneda2 = substr($this->codigo_moneda2, -1);   

            $precio_moneda2 = $this->calculatePrice($this->codigo_moneda2); 
        }  

        $u = array(
            'id_usuario' => $this->id_usuario,
            'codigo_moneda' => $this->codigo_moneda,
            'precio_moneda1' => $precio_moneda1,
            'tipo' => $this->tipo,
            'id_sucursal' => $this->id_sucursal,
            'id_usuario2' => $this->id_usuario2,
            'codigo_moneda2' => $this->codigo_moneda2,
            'precio_moneda2' => $precio_moneda2,
            'fecha' => time()
        );


        #Array con datos validos para el update
        $data = array();

        #Valida que los datos no esten vacios y los inserta en el array "data"
        foreach ($u as $key=>$val) {
            if(NULL !== $u[$key] && !Functions::emp($u[$key])){
                $data[$key] = $u[$key];
            }
        }

        
        $this->checkTransaction();

        # Crea una transacción
        $id_transaccion =  $this->db->insert('transaccion',$data);

            return array('success' => 1, 'message' => 'Transacción creada con éxito!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }


    /**
     * Edita usuarios 
     * 
     * @return array
    */ 
   /* public function edit() : array {
        try {
            global $http;

            $id = $http->request->get('id_transaccion');

            $this->errors(true);

            $data = array(
                'nombre' => $this->nombre
            );

            #Edita un origen
            $this->db->update('origen',$data,"id_origen = '$id'",'1');

            return array('success' => 1, 'message' => 'Origen editado con éxito!');

        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }*/

    /**
     * Obtiene elementos de transaccion seg
     *    
     *
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

    //si el usuario es 2 traer moneda 2
    public function getByUser(int $id_user){
        $inner = "INNER JOIN moneda m1 ON m1.codigo=transaccion.codigo_moneda
                  LEFT  JOIN moneda m2 ON m2.codigo=transaccion.codigo_moneda2
                  LEFT JOIN sucursal s ON s.id_sucursal=transaccion.id_sucursal";

        return $this->db->select('transaccion.fecha,transaccion.tipo,transaccion.id_usuario,s.nombre,m1.codigo as m1,m2.codigo as m2',
                                 'transaccion',$inner,"transaccion.id_usuario='$id_user' OR transaccion.id_usuario2='$id_user'");

    }



    /**
     * Eliminar usuario
    */
    /*final public function del() {

       Global $config;

      $res = $this->db->delete('origen',"id_origen='$this->id'",'1');

      # Redireccionar al controlador usuarios con un success=true
      Functions::redir($config['build']['url'] . 'origen/&success=true');
    }*/

    /**
     * __construct()
    */
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
        $this->startDBConexion();
    }
}