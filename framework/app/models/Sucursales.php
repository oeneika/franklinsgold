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


    /**
     * Verifica errores en el formulario
     * 
     */
    private function errors($edit = false){
        global $http;
        $this->data = $http->request->all();

        #Verifica que los campos no esten vacios
        if(Functions::emp($this->data['nombre'])){
            throw new ModelsException("El nombre no debe estar vacio");
        }

        if(strlen($this->data['nombre']) > 45){
            throw new ModelsException("El nombre no debe tener más de 45 carácteres");
        }

        if(Functions::emp($this->data['direccion'])){
            throw new ModelsException("La direccion no debe estar vacia");
        }

        if(strlen($this->data['direccion']) > 45){
            throw new ModelsException("La dirección no debe tener más de 45 carácteres");
        }

        #Verifica si ya exitse un tienda con ese nombre
        $nombre = $this->db->scape($this->data['nombre']);
        $sucursales = $this->db->select('nombre','sucursal',null,"nombre = '$nombre'");

        if(false !== $sucursales && false === $edit){
            throw new ModelsException("Ya existe una sucursal con ese nombre");
        }
                    
    }

    /**
     * Crear una nueva sucursal
     * 
     * @return array
    */ 
    public function add() : array {
        try {
            $this->errors();
            $id = $this->db->insert('sucursal',array(
                'nombre'=>$this->data['nombre'],
                'direccion'=>$this->data['direccion']
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
            $id = $this->db->scape($this->data['id_sucursal']);
            $filas=  $this->db->update('sucursal',array(
                'nombre'=>$this->data['nombre'],
                'direccion'=>$this->data['direccion']
            ),
            "id_sucursal = $id");
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
        $this->db->delete('sucursal',"id_sucursal=$this->id");

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