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
 * Modelo Sucursales
 */
class Sucursales extends Models implements IModels {
    use DBModel;

    /**
     * Data de la sucursal
     */
    private $data;
    private $id_sucursal;

    /**
     * Verifica errores en el formulario
     * 
     */
    private function errors($edit = false){
        global $http;
        $this->data = $http->request->all();

        #Si es editar trae el id de la sucursal para validar el nombre de la misma
        $this->id_sucursal = 0;

        if($edit){
            $this->id_sucursal = $this->data['id_sucursal'];
        }


        #Verifica que los campos no esten vacios
        if(Functions::emp($this->data['nombre'])){
            throw new ModelsException("El nombre no debe estar vacío");
        }else{

            $nombre_sucursal = $this->db->scape($this->data['nombre']);
            $a = $this->db->select('id_sucursal','sucursal',null,"nombre='$nombre_sucursal'");

            if($a!=false and $this->id_sucursal!=$a[0]["id_sucursal"] ){
                throw new ModelsException("El nombre de la sucursal ya existe");
            }

        }

        if(strlen($this->data['nombre']) > 45){
            throw new ModelsException("El nombre no debe tener más de 45 carácteres");
        }

        if (!ctype_digit($this->data['telefono'])){
            throw new ModelsException("Teléfono inválido, debe ser numérico.");              
        }

        if (strlen($this->data['telefono']) < 11){
            throw new ModelsException("Teléfono invalido, debe tener al menos 11 dígitos");              
        }

        if(Functions::emp($this->data['direccion'])){
            throw new ModelsException("La direccion no debe estar vacía");
        }

        if(strlen($this->data['direccion']) > 45){
            throw new ModelsException("La dirección no debe tener más de 45 carácteres");
        }

        if( strpos($this->data['nombre'],' ') !== false ){
            throw new ModelsException('El nombre no puede tener espacios en blanco');
        }       
    }

    /**
     * Crear una nueva sucursal y a su usuario respectivo
     * 
     * @return array
    */ 
    public function add() : array {
        try {
            $this->errors();

            $u = array(
                'primer_nombre' => $this->data['nombre'],
                'primer_apellido' => $this->data['nombre'],
                'usuario' => $this->data['nombre'],
                'pass' => Helper\Strings::hash(123),
                'sexo' => 'm',
                'telefono' => $this->data['telefono'],
                'email' => $this->data['nombre'].'@franklingolds.com',
                'tipo' => 1,
                'es_sucursal' => 1
            );

            # Crea al usuario
            $id_user =  $this->db->insert('users',$u);

            # Crea la sucursal
            $id_sucursal = $this->db->insert('sucursal',array(
                'nombre'=> $this->data['nombre'],
                'direccion'=> $this->data['direccion'],
                'telefono'=> $this->data['telefono'],
                'id_user' => $id_user
            ));

            return array('success' => 1, 'message' => 'Sucursal creada con éxito!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }

    /**
     * Edita una sucursal
     * 
     * @return array
     */
    public function edit(){
        try {
            $this->errors(true);
         
            $id_user = $this->data['id_user'];

            #Edita la sucursal
            $this->db->update('sucursal',array(
                'nombre'=>$this->data['nombre'],
                'telefono'=> $this->data['telefono'],
                'direccion'=>$this->data['direccion']
            ),"id_sucursal = $this->id_sucursal");

            #Edita al usuario
            $this->db->update('users',array(
                'primer_nombre' => $this->data['nombre'],
                'primer_apellido' => $this->data['nombre'],
                'usuario' => $this->data['nombre'],
                'telefono' => $this->data['telefono'],
                'email' => $this->data['nombre'].'@franklingolds.com'
            ),"id_user = '$id_user'");

            return array('success' => 1, 'message' =>"Sucursal editada con éxito!");
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }

    /**
     * Elimina una sucursal
     * 
     * @return array
     */
    public function del(){
        global $config;

        $id_sucursal = $this->db->scape($this->id);

        #Trae el id del usuario
        $id_user = $this->db->select('id_user','sucursal',null,"id_sucursal='$id_sucursal'")[0]["id_user"];

        #Elimina la sucursal y el usuario correspondiente a la sucursal
        $this->db->delete('users',"id_user=$id_user");
        $this->db->delete('sucursal',"id_sucursal=$id_sucursal");
       
        Functions::redir($config['build']['url'] . 'sucursal/&success=true');
    }

    /**
     * Trae todas las sucursales
     * 
     * @return array
     */
    public function get(){
        return $this->db->select('*','sucursal');
    }


    /**
     * __construct()
    */
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
		$this->startDBConexion();
    }
}