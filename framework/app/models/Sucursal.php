<?php


namespace app\models;

use app\models as Model;
use Ocrend\Kernel\Models\Models;
use Ocrend\Kernel\Models\IModels;
use Ocrend\Kernel\Models\ModelsException;
use Ocrend\Kernel\Models\Traits\DBModel;
use Ocrend\Kernel\Router\IRouter;

/**
 * Modelo Sucursal
 *
 * @author Alexander De Azevedo
 */

class Sucursal extends Models implements IModels {
    /**
      * Característica para establecer conexión con base de datos. 
    */
    use DBModel;

    private $nombre;
    private $direccion;

    
    /**
      * Controla los errores de entrada del formulario
      *
      * @throws ModelsException
    */
    final private function errors() {
      global $http;
      $this->nombre = $http->request->get('nombre');
       $this->direccion  = $http->request->get('direccion');

      if($this->functions->e($this->nombre,$this->direccion)){
        throw new ModelsException('Todos los campos son obligatorios!');
      }
      # throw new ModelsException('¡Esto es un error!');
    }

    /** 
      * Crea un elemento de Sucursal en la tabla ``
      *
      * @return array con información para la api, un valor success y un mensaje.
    */
    final public function add() {
      try {
        global $http;
                  
        # Controlar errores de entrada en el formulario
        $this->errors();

        # Insertar elementos
        $this->db->query("INSERT INTO sucursal
        (nombre,direccion)
        VALUES ($this->nombre,$this->direccion);");

        return array('success' => 1, 'message' => 'Creado con éxito.');
      } catch(ModelsException $e) {
        return array('success' => 0, 'message' => $e->getMessage());
      }
    }
          
    /** 
      * Edita un elemento de Sucursal en la tabla ``
      *
      * @return array con información para la api, un valor success y un mensaje.
    */
    final public function edit() : array {
      try {
        global $http;
                  
        # Controlar errores de entrada en el formulario
        $this->errors();

        # Actualizar elementos
        $this->db->query("UPDATE sucursal
        SET color_short = '$this->color_short', color_camisa = '$this->color_camisa', tipo = $this->tipo
        WHERE anio_ini_color = $this->anio_ini AND anio_fin_color = $this->anio_fin AND codigo_producto = $this->uniforme");

        return array('success' => 1, 'message' => 'Editado con éxito.');
      } catch(ModelsException $e) {
        return array('success' => 0, 'message' => $e->getMessage());
      }
    }

    /** 
      * Borra un elemento de Sucursal en la tabla ``
      * y luego redirecciona a sucursal/&success=true
      *
      * @return void
    */
    final public function delete() {
      global $config;
      # Borrar el elemento de la base de datos
      $this->db->query("DELETE FROM sucursal_4 WHERE id_color = $this->id");
      # Redireccionar a la página principal del controlador
      $this->functions->redir($config['site']['url'] . 'sucursal/&success=true');
    }

  /**
      * Obtiene elementos de la tabla "Sucursal"
      *
      * @param $select: Elementos de a seleccionar
      *
      * @return false|array: false si no hay datos.
      *                      array con los datos.
      */
    final public function get($select = '*') {
      /*Busqueda personalizada*/
    return $this->db->select($select,'sucursal');
    }


    /**
      * __construct()
    */
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
        $this->startDBConexion();
    }

}