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
 * Modelo Transaccion
 */
class Transaccion extends Models implements IModels {
    
    use DBModel;
    private $id_usuario;
    private $codigo;
    private $id_sucursal;

    /**
     * Revisa errores en el formulario
     * 
     * @return exception
    */ 
    private function errors(bool $edit = false){
        global $http;

        $this->id_usuario = $http->request->get('id_usuario');
        $this->codigo = $http->request->get('codigo');
        $this->id_sucursal = $http->request->get('id_sucursal');
        
        # Verificar que no están vacíos
        if (Helper\Functions::e($this->nombre,$this->codigo,$this->id_sucursal)) {
            throw new ModelsException('Debe seleccionar todos los elementos.');
        }

    }
    /**
     * Agrega usuarios 
     * 
     * @return array
    */ 
    public function add() : array {
        try {

            #Revisa errores del formulario
            $this->errors();

            # Registrar al usuario
            $id_transaccion =  $this->db->insert('transaccion',array(
                'id_usuario' => $this->id_usuario,
                'codigo' => $this->codigo,
                'id_sucursal' => $this->id_sucursal
            ));

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
    public function getTransacciones(int $tipo = 0,string $select = '*') {

        if($tipo == 1){
            return $this->db->select($select,'transaccion','tipo=0');
        }else 
        if($tipo == 2){
            return $this->db->select($select,'transaccion','tipo=0');
        }

        return $this->db->select($select,'transaccion');
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