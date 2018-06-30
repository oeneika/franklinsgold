<?php


namespace app\models;

use app\models as Model;
use Ocrend\Kernel\Models\Models;
use Ocrend\Kernel\Models\IModels;
use Ocrend\Kernel\Models\ModelsException;
use Ocrend\Kernel\Models\Traits\DBModel;
use Ocrend\Kernel\Router\IRouter;

/**
 * Modelo Usuarios
 *
 * @author Alexander De Azevedo
 */

class Usuarios extends Models implements IModels {
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



    final public function crear(){
      $this->db->insert('usuario',array(
        'primer_nombre' => $http->request->get('primer_nombre'),
        'segundo_nombre' => $http->request->get('segundo_nombre'),
        'primer_apellido' => $http->request->get('primer_apellido'),
        'segundo_apellido' => $http->request->get('segundo_apellido'),
        'usuario' => $http->request->get('usuario'),
        'sexo' => $http->request->get('sexo'),
        'telefono' => $http->request->get('telefono'),
        'correo' => $http->request->get('correo')
      ));
    }

    final public function editar(){
     


      try {
        global $http;
        $this->codigo = $http->request->get('id_usuario');
        
        # Controlar errores de entrada en el formulario
        $this->errors(true);

        # Actualizar elementos
         $this->db->update('usuario',array(
        'nombre' => $http->request->get('primer_nombre'),
      ),"idusuario='$this->id'",'LIMIT 1');

        return array('success' => 1, 'message' => 'Editado con éxito.');
      } catch(ModelsException $e) {
        return array('success' => 0, 'message' => $e->getMessage());
      }

    }

    final public function eliminar(){
      $this->db->delete('usuario',"idusuario='$this->id'");
      $this->functions->redir($config['site']['url'] . 'clientes/&success=true');
    }

  /**
      * Obtiene elementos de la tabla "Usuarios"
      *
      * @param $select: Elementos de a seleccionar
      *
      * @return false|array: false si no hay datos.
      *                      array con los datos.
      */
    final public function get($select = '*') {
      /*Busqueda personalizada*/
    return $this->db->select($select,'usuario');
    }


    /**
      * __construct()
    */
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
        $this->startDBConexion();
    }
}