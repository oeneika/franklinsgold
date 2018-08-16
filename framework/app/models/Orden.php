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

    /**
     * Revisa errores en el formulario
     * 
     * @return exception
    */ 
    private function errors(bool $edit = false){
        global $http;

        #Obtener los datos $_POST
        $this->id_usuario = $http->request->get('id_usuario');
        $this->tipo_gramo = $http->request->get('tipo_gramo');     
        $this->cantidad = $http->request->get('cantidad');
        $this->id_sucursal = $http->request->get('id_sucursal');
        $this->tipo_orden = $http->request->get('tipo_orden');

        #Usada en caso de ser una compra/venta vía movil
        $email = $http->request->get('email');
        if ( !Helper\Functions::emp($email)) {
            $email = $this->db->scape($email);

            $this->id_usuario = $this->db->select("id_user","users",null,"email='$email'")[0]["id_user"];
            
        }

        $this->id_moneda = $http->request->get('id_moneda');

        # Verificar que no están vacíos
        if (Helper\Functions::e($this->id_usuario,$this->tipo_gramo,$this->cantidad,$this->id_sucursal,$this->tipo_orden)) {
            throw new ModelsException('Debe seleccionar todos los elementos.');
        }

        if($this->cantidad<0.1){
            throw new ModelsException('Debe introducir características válidas.');
        }

        if( $this->tipo_gramo != "oro" and $this->tipo_gramo != "plata" ){
            throw new ModelsException('Tipo de gramo inválido.');
        }

        if( $this->tipo_orden != 1 and $this->tipo_orden != 2 and $this->tipo_orden != 3 ){
            throw new ModelsException('Tipo de orden inválida.');
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
                
                if(Helper\Functions::e($this->id_moneda)){
                    throw new ModelsException('Debe seleccionar todos los elementos.');
                }
                
                $moneda = $this->db->select("peso,composicion","moneda",null,"codigo='$this->id_moneda'");
                $peso = $moneda[0]["peso"];
                $composicion = $moneda[0]["composicion"];

                if($this->tipo_gramo !== $composicion){
                    throw new ModelsException('Las composiciones no coinciden.');
                }

                if($peso != $this->cantidad){
                    throw new ModelsException('Los gramos de oro a intercambiar no coinciden con el peso de la moneda.');
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

            $orden = array(
                'id_usuario' => $this->id_usuario,
                'tipo_gramo' => $this->tipo_gramo,
                'cantidad' => $this->cantidad,
                'id_sucursal' => $this->id_sucursal,
                'tipo_orden' => $this->tipo_orden,
                'fecha' => time(),
                'estado' => 1
            );
            
            if(NULL !== $this->id_moneda && !Functions::emp($this->id_moneda)){
                $orden["codigo_moneda"] = $this->id_moneda;
            }
           
           #Crea una transaccion
           $id_orden =  $this->db->insert('orden',$orden);
                    
            return array('success' => 1, 'message' => 'Orden creada con exito!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
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

            if($orden == false){
                Functions::redir($config['build']['url'] . 'ordenadmin/&success=false');
            }


            #Trae la cartera si existe
            $id_usuario = $orden[0]["id_usuario"];
            $tipo_gramo = $orden[0]["tipo_gramo"];
            $cantidad_orden = $orden[0]["cantidad"];
            $cartera = $this->db->select("id_usuario_gramo,cantidad","user_gramo",null,"id_usuario='$id_usuario' and tipo_gramo='$tipo_gramo'");


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
                    'estado'=>2
                ),"id_orden = $this->id");

                Functions::redir($config['build']['url'] . 'ordenadmin/&success=true');

            }else{

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
                            'estado'=>2
                        ),"id_orden = $this->id");

                        Functions::redir($config['build']['url'] . 'ordenadmin/&success=true');
                    }else{

                        Functions::redir($config['build']['url'] . 'ordenadmin/&success=false');

                    }

                }
                
            }       
    }


    /**
     * Trae las ordenes
     * 
     * @return array
     */
    public function get(string $where="1=1",$limit=null,$extra=''){

        $inner = "INNER JOIN users u ON u.id_user = orden.id_usuario
                  INNER JOIN sucursal s On s.id_sucursal = orden.id_sucursal";

        return $this->db->select("orden.*,s.nombre as nombre_sucursal,u.primer_nombre,u.primer_apellido,u.numero_cuenta","orden",$inner,$where,$limit,$extra);

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

        return $this->db->select("cantidad","user_gramo",null,"tipo_gramo='$composicion' and $where")[0]["cantidad"];

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
                  
        return $this->get("orden.estado=2 and orden.tipo_gramo='$tipo_gramo' and orden.tipo_orden='$tipo' and u.id_user='$id_usuario'",
        5,"ORDER BY orden.id_orden DESC");
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