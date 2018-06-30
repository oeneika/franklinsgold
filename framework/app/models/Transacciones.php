<?php


namespace app\models;

use app\models as Model;
use Ocrend\Kernel\Models\Models;
use Ocrend\Kernel\Models\IModels;
use Ocrend\Kernel\Models\ModelsException;
use Ocrend\Kernel\Models\Traits\DBModel;
use Ocrend\Kernel\Router\IRouter;

/**
 * Modelo Transacciones
 *
 * @author Alexander De Azevedo
 */

class Transacciones extends Models implements IModels {
    /**
      * Característica para establecer conexión con base de datos. 
    */
    use DBModel;

    private $codigo;

    
    /**
      * Controla los errores de entrada del formulario
      *
      * @throws ModelsException
    */
    final private function errors() {
      global $http;
      $this->anio_ini = $http->request->get('anio_ini');

      if($this->functions->e($this->anio_ini,$this->anio_fin,$this->color_short,$this->color_camisa,$this->uniforme,$this->tipo)){
        throw new ModelsException('Todos los campos son obligatorios!');
      }
      # throw new ModelsException('¡Esto es un error!');
    }

    /** 
      * Crea un elemento de Transacciones en la tabla ``
      *
      * @return array con información para la api, un valor success y un mensaje.
    */
    final public function add() {
      try {
        global $http;
                  
        # Controlar errores de entrada en el formulario
        $this->errors();

        # Insertar elementos
        $this->db->query("INSERT INTO transacciones_4
        (anio_ini_color,anio_fin_color,color_short,color_camisa,codigo_producto,tipo)
        VALUES ($this->anio_ini,$this->anio_fin, '$this->color_short', '$this->color_camisa', $this->uniforme,$this->tipo);");

        return array('success' => 1, 'message' => 'Creado con éxito.');
      } catch(ModelsException $e) {
        return array('success' => 0, 'message' => $e->getMessage());
      }
    }
          
    /** 
      * Edita un elemento de Transacciones en la tabla ``
      *
      * @return array con información para la api, un valor success y un mensaje.
    */
    final public function edit() : array {
      try {
        global $http;
                  
        # Controlar errores de entrada en el formulario
        $this->errors();

        # Actualizar elementos
        $this->db->query("UPDATE transacciones
        SET color_short = '$this->color_short', color_camisa = '$this->color_camisa', tipo = $this->tipo
        WHERE anio_ini_color = $this->anio_ini AND anio_fin_color = $this->anio_fin AND codigo_producto = $this->uniforme");

        return array('success' => 1, 'message' => 'Editado con éxito.');
      } catch(ModelsException $e) {
        return array('success' => 0, 'message' => $e->getMessage());
      }
    }

    /** 
      * Borra un elemento de Transacciones en la tabla ``
      * y luego redirecciona a transacciones/&success=true
      *
      * @return void
    */
    final public function delete() {
      global $config;
      # Borrar el elemento de la base de datos
      $this->db->query("DELETE FROM transacciones_4 WHERE id_color = $this->id");
      # Redireccionar a la página principal del controlador
      $this->functions->redir($config['site']['url'] . 'transacciones/&success=true');
    }

  /**
      * Obtiene elementos de la tabla "Transacciones"
      *
      * @param $select: Elementos de a seleccionar
      *
      * @return false|array: false si no hay datos.
      *                      array con los datos.
      */
    final public function get($select = '*') {
      /*Busqueda personalizada*/
    return $this->db->select($select,'transaccion');
    }


    /**
      * __construct()
    */
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
        $this->startDBConexion();
    }

}