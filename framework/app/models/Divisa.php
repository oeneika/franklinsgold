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
 * Modelo Divisa
 */
class Divisa extends Models implements IModels {
    use DBModel;
    private $nombre;
    private $precio_dolares;

    /**
     * Revisa errores en el formulario
     * 
     * @return exception
    */ 
    private function errors(bool $edit = false){
        global $http;
        $this->nombre = $http->request->get('nombre');
        $this->precio_dolares = $http->request->get('precio_dolares');
        
        # Verificar que no estan vacíos
        if (Helper\Functions::emp($this->nombre)) {
            throw new ModelsException('Debe introducir un nombre.');
        }

        if (Helper\Functions::emp($this->precio_dolares)) {
            throw new ModelsException('Debe introducir un precio.');
        }

        if ( $this->precio_dolares<0 ) {
            throw new ModelsException('Debe introducir un precio válido.');
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
            $id_divisa =  $this->db->insert('divisa',array(
                'nombre_divisa' => $this->nombre,
                'precio_dolares' => $this->precio_dolares
            ));

            return array('success' => 1, 'message' => 'Dívisa creada con éxito!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }
    /**
     * Edita usuarios 
     * 
     * @return array
    */ 
    public function edit() : array {
        try {
            global $http;

            $id = $http->request->get('id_divisa');

            $this->errors();

            $data = array(
                'nombre_divisa' => $this->nombre,
                'precio_dolares' => $this->precio_dolares
            );

            #Edita un origen
            $this->db->update('divisa',$data,"id_divisa = '$id'",'1');

            return array('success' => 1, 'message' => 'Dívisa editada con éxito!');

        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }
    /**
     * Obtiene a todos los origenes
     *    
     *
     * @return false|array con información de los usuarios
     */  
    public function getDivisas(string $select = '*') {
        return $this->db->select($select,'divisa');
    }

    /**
     * Eliminar usuario
    */
    final public function del() {

       Global $config;

      $res = $this->db->delete('divisa',"id_divisa='$this->id'",'1');

      # Redireccionar al controlador usuarios con un success=true
      Helper\Functions::redir($config['build']['url'] . 'divisa/&success=true');
    }

    /**
     * __construct()
    */
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
        $this->startDBConexion();
    }
}