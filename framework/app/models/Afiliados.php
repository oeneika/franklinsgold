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

/**
 * Modelo Afiliados
 */
class Afiliados extends Models implements IModels {
    use DBModel;

    /**
     * Array en el que se guardaran datos del comercio
     */
    private $data;
    private $id_comercio_afiliado;

    /**
     * Errores en telefonos
     */
    private function errorsTel(){
        if (!array_key_exists('telefono',$this->data)) {
            throw new ModelsException('Campo teléfono no definido.');
        }
        foreach ($this->data['telefono'] as $key => $value) {

            if (Helper\Functions::emp($value)){
                throw new ModelsException('Uno o más telefonos estan vacíos.');
            }

            if (!ctype_digit($value)){
                throw new ModelsException("Teléfono inválido, debe ser numérico.");              
            }

            if (strlen($value) < 11){
                throw new ModelsException("Teléfono inválido, debe tener al menos 11 dígitos.");              
            }

        }
    }

    /**
     * Función para verificar errores en el formulario
     */
    private function errors($edit = false){
        global $http;

        $this->data = $http->request->all();

        #Si es editar trae el id del comercio afiliado para validar el nombre del mismo
        $this->id_comercio_afiliado = 0;
        
        if($edit){
            $this->id_comercio_afiliado = $this->data['id_comercio_afiliado'];
        }

        if (!array_key_exists('nombre',$this->data) || Helper\Functions::emp($this->data['nombre'])) {
            throw new ModelsException('El nombre no debe estar vacío.');
        }else{

            $sucursal_comercio_afiliado= $this->db->scape($this->data['sucursal']);
            $a = $this->db->select('id_comercio_afiliado','comercio_afiliado',null,"sucursal='$sucursal_comercio_afiliado'");

            if($a!=false and $this->id_comercio_afiliado!=$a[0]["id_comercio_afiliado"] ){
                throw new ModelsException("El nombre de la sucursal del comercio ya existe.");
            }

        }

        if (!array_key_exists('sucursal',$this->data)  || Helper\Functions::emp($this->data['sucursal']) ) {
            throw new ModelsException('La sucursal no debe estar vacía.');
        }

        if (!array_key_exists('direccion',$this->data)  || Helper\Functions::emp($this->data['direccion']) ) {
            throw new ModelsException('La direccion no debe estar vacía.');
        }

        /*if( strpos($this->data['nombre'],' ') !== false ){
            throw new ModelsException('El nombre no puede tener espacios en blanco.');
        }

        if( strpos($this->data['sucursal'],' ') !== false ){
            throw new ModelsException('La sucursal no puede tener espacios en blanco.');
        }*/

        

        $this->errorsTel();
    }

    /**
     * Inserta telefonos
     */
    private function addTelefonos($id_afiliado){

        foreach ($this->data['telefono'] as $key => $value) {
            $this->db->insert('telefono',array(
                'id_comercio_afiliado' => $id_afiliado,
                'telefono'=> $value
            ));
        }

    }

    /**
     * Crear comercio
     * 
     * @return array
    */ 
    public function add() : array {
        try {

            $this->errors();

            $u = array(
                'primer_nombre' => $this->data['nombre'],
                'primer_apellido' => $this->data['sucursal'],
                'usuario' => $this->data['sucursal'],
                'pass' => Helper\Strings::hash(123),
                'sexo' => 'm',
                'telefono' => $this->data['telefono'][0],
                'email' => $this->data['sucursal'].'@franklingolds.com',
                'tipo' => 1,
                'es_comercio_afiliado' => 1
            );

            #Crea el usuario
            $id_user =  $this->db->insert('users',$u);

            #Crea el comercio afiliado
            $id = $this->db->insert('comercio_afiliado', array(
                'nombre' => $this->data['nombre'],
                'sucursal' => $this->data['sucursal'],
                'direccion' => $this->data['direccion'],
                'id_user' => $id_user
            ));

            $this->addTelefonos($id);


            return array('success' => 1, 'message' => 'Comercio creado con éxito!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }


     /**
     * Edita un comercio
     * 
     * @return array
     */
    public function edit() : array {
        try {

            $this->errors(true);

            $id_user = $this->db->scape($this->data['id_user']);

            #Trae el nombre pa cambiarlo a todas las sucursales
            $nombre_actual = $this->db->select("nombre","comercio_afiliado",null,"id_comercio_afiliado='$this->id_comercio_afiliado'")[0]["nombre"];

            #Edita el comercio afiliado
            $this->db->update('comercio_afiliado', array(
                'nombre' => $this->data['nombre'],
                'sucursal' => $this->data['sucursal'],
                'direccion' => $this->data['direccion']
            ),"id_comercio_afiliado = $this->id_comercio_afiliado");


            #Edita todos todos los comercios afiliados con el mismo nombre
            $nombre = $this->data['nombre'];
            $this->db->update('comercio_afiliado', array(
                'nombre' => $this->data['nombre']
            ),"nombre = '$nombre_actual'");

  
            #Edita el usuario
            $this->db->update('users',array(
                'primer_nombre' => $this->data['nombre'],
                'primer_apellido' => $this->data['sucursal'],
                'usuario' => $this->data['sucursal'],
                'telefono' => $this->data['telefono'][1],
                'email' => $this->data['sucursal'].'@franklingolds.com'
            ),"id_user = '$id_user'");

            #Edita todos todos los usuarios con el mismo nombre
            $this->db->update('users', array(
                'primer_nombre' => $this->data['nombre']
            ),"primer_nombre = '$nombre_actual' and es_comercio_afiliado=1");


            
            #Elimina la relacion con los telefonos
            $this->db->delete('telefono',"id_comercio_afiliado='$this->id_comercio_afiliado'");

            #Crea una nueva relacion con los telefonos
            $this->addTelefonos($this->id_comercio_afiliado);

            return array('success' => 1, 'message' => 'Comercio editado con éxito!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }


    /**
     * Crear un intercambio
     * 
     * @return array
    */ 
    public function addIntercambio() : array {
        try {
            global $http;

            #Data POST del formulario
            $data_form = $http->request->all();

            #Se buscan los datos de la moneda en la DB
            $data_moneda = $this->db->select('composicion,peso','moneda',null,'codigo = '. $data_form['moneda']);

            $composicion = $data_moneda[0]['composicion'] == 'oro' ? 'gold' : 'silver';
            
            #Url de iragold
            $url = 'https://goldiraguide.org/wp-admin/admin-ajax.php';
    
            #Se procede a hacer la peticion a la api
            $opt = array(
                CURLOPT_POST =>true,
                CURLOPT_RETURNTRANSFER =>true,
                CURLOPT_POSTFIELDS =>['action' => 'getMetalPrice', 'api_key' => 'anonymous'],
                CURLOPT_URL => $url
            );
            $ch = curl_init();
            curl_setopt_array($ch,$opt);
            $result = curl_exec($ch);     
            curl_close($ch);
            $result = json_decode($result, true);
            $data = $result['buttonFrame'][$composicion]['1m']['data'];

            #Se calcula el valor de la moneda
            $precio_dolares = $data_moneda[0]['peso'] * ($data[29]/28.3495);


            #Se inserta en la DB
            $id = $this->db->insert('afiliado_moneda', array(
                'codigo' => $data_form['moneda'],
                'id_comercio_afiliado' => $data_form['id_comercio_afiliado'],
                'monto' => $precio_dolares,
                'fecha' => time(),
            ));


            return array('success' => 1, 'message' => 'Intercambio creado con éxito!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }

    /**
     * Trae todos los comercios en la db
     */
    public function get($select = '*'){
        return $this->db->select($select,'comercio_afiliado');
    }

    /**
     * Trae todos los telefonos de un comercio en la db
     */
    public function getTelefonos($id){
        return $this->db->select('telefono','telefono',null,"id_comercio_afiliado = $id");
    }

    /**
     * Elimina comercio afiliado
     */
    public function del(){
        global $config;

        $id_comercio_afiliado = $this->db->scape($this->id);

        #Trae el id del usuario
        $id_user = $this->db->select('id_user','comercio_afiliado',null,"id_comercio_afiliado='$id_comercio_afiliado'")[0]["id_user"];

        #Elimina el comercio afiliado, sus telefonos y el usuario correspondiente al mismo
        $this->db->delete('users',"id_user=$id_user");
        $this->db->delete('telefono',"id_comercio_afiliado=$id_comercio_afiliado");
        $this->db->delete('comercio_afiliado',"id_comercio_afiliado=$id_comercio_afiliado");
        
        Helper\Functions::redir($config['build']['url'] . "afiliados/&success=true");
        
    }


    /**
     * __construct()
    */
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
		$this->startDBConexion();
    }
}