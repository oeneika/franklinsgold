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
 * Modelo Origen
 */
class Origen extends Models implements IModels {

    use DBModel;
    private $nombre;
    private $abreviatura;
    /**
     * Revisa errores en el formulario
     * 
     * @return exception
    */ 
    private function errors(bool $edit = false){
        global $http;
        $this->nombre = $http->request->get('nombre');
        $this->abreviatura = $http->request->get('abreviatura');
        
        # Verificar que no están vacíos
        if (Helper\Functions::emp($this->nombre)) {
            throw new ModelsException('Debe introducir un nombre.');
        }

        if (Helper\Functions::emp($this->abreviatura)) {
            throw new ModelsException('Debe introducir un abreviatura.');
        }

        if (strlen($this->abreviatura) !== 3){
            throw new ModelsException('La abreviatura debe tener exactamente 3 caractéres.');
        }

        if ($this->db->select('abreviatura','origen',null,"abreviatura = '$this->abreviatura'")){
            throw new ModelsException('La abreviatura ya existe.');
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
            $id_origen =  $this->db->insert('origen',array(
                'nombre' => $this->nombre,
                'abreviatura' => $this->abreviatura
            ));

            return array('success' => 1, 'message' => 'Origen creado con éxito!');
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

            $id = $http->request->get('id_origen');

            $this->errors(true);

            $data = array(
                'nombre' => $this->nombre,
                'abreviatura' => $this->abreviatura
            );

            #Edita un origen
            $this->db->update('origen',$data,"id_origen = '$id'",'1');

            return array('success' => 1, 'message' => 'Origen editado con éxito!');

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
    public function getOrigenes(string $select = '*') {
        return $this->db->select($select,'origen');
    }

    /**
     * Eliminar usuario
    */
    final public function del() {

       Global $config;

      $res = $this->db->delete('origen',"id_origen='$this->id'",'1');

      # Redireccionar al controlador usuarios con un success=true
      Functions::redir($config['build']['url'] . 'origen/&success=true');
    }

    /**
     * __construct()
    */
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
        $this->startDBConexion();
    }
}