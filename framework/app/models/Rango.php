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
 * Modelo Rangos
 */
class Rango extends Models implements IModels {
    
    use DBModel;

    private $id_rango;
    private $nombre_rango;
    private $monto_diario;

    /**
     * Revisa errores en el formulario
     * 
     * @return exception
    */ 
    private function errors(bool $edit = false){

        global $http;

        $this->nombre_rango = $http->request->get('nombre_rango');
        $this->monto_diario = $http->request->get('monto_diario'); 
        
        # Verificar que no están vacíos
        if (Helper\Functions::emp($this->nombre_rango) and !$edit){
            throw new ModelsException('Debe llenar el nombre del rango.');
        }

        if (Helper\Functions::emp($this->monto_diario)){
            throw new ModelsException('Debe llenar el monto diario del rango.');
        }

        $this->nombre_rango = $this->db->scape($this->nombre_rango);
        $nr = $this->db->select('nombre_rango','rango',null,"nombre_rango='$this->nombre_rango'");

        if ( $nr!= false and !$edit){
            throw new ModelsException('El nombre del rango ya existe.');
        }

        if ( $this->monto_diario  < 0 ){
            throw new ModelsException('Ingrese un monto válido.');
        }


    }

    /**
     * Agrega rangos 
     * 
     * @return array
    */ 
    public function add() : array {
        try {
            global $http, $config;

            #Revisa errores del formulario
            $this->errors();

            $r = array(
            'nombre_rango' => $this->nombre_rango,
            'monto_diario' => $this->monto_diario
            );

            # Insertamos el rango
            $this->db->insert('rango',$r);

            return array('success' => 1, 'message' => 'Rango creado con éxito!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }


    /**
     * Edita rangos 
     * 
     * @return array
    */ 
    public function edit() : array {
        try {

            global $http;

            $this->id_rango = $http->request->get('id_rango');

            $this->errors(true);

            $r = array(
                /*'nombre_rango' => $this->nombre_rango,*/
                'monto_diario' => $this->monto_diario
            );

            #Edita un rango
            $this->db->update('rango',$r,"id_rango = '$this->id_rango'",'1');

            return array('success' => 1, 'message' => 'Rango editado con éxito!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }


    /**
     * Obtiene elementos de rango
     *   
     * @param select :  elementos a traer en la consulta
     *
     * @return false|array con información de los usuarios
     */  
    public function get(string $select = '*') {
  
        return $this->db->select($select,'rango');
    }


    /**
     * Eliminar rango
    */
    final public function del() {
        Global $config;

       $res = $this->db->delete('rango',"id_rango='$this->id'",'1');

      # Redireccionar al controlador usuarios con un success=true
      Helper\Functions::redir($config['build']['url'] . 'rango/&success=true');

    }

    /**
     * __construct()
    */
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
        $this->startDBConexion();
    }
}