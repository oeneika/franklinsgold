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

    private $primer_nombre;
    private $segundo_nombre;
    private $primer_apellido;
    private $segundo_apellido;
    private $usuario;
    private $pass;
    private $sexom;
    private $sexof;
    private $telefono;
    private $correo;
    
        /**
          * Controla los errores de entrada del formulario
          *
          * @throws ModelsException
        */
        final private function errors(bool $edit = false) {
          global $http;
            # Obtener los datos $_POST
            $this->primer_nombre = $http->request->get('primer_nombre');
            $this->segundo_nombre = $http->request->get('segundo_nombre');
            $this->primer_apellido = $http->request->get('primer_apellido');
            $this->segundo_apellido = $http->request->get('segundo_apellido');
            $this->usuario = $http->request->get('usuario');
            $this->pass = $http->request->get('pass');
            $this->sexom = $http->request->get('masculinoRadio');
            $this->sexof = $http->request->get('femeninoRadio');
            $this->telefono = $http->request->get('telefono');
            $this->email = $http->request->get('email');

            # Verificar que no están vacíos
            if (Helper\Functions::e($primer_nombre, $segundo_nombre, $primer_apellido,
            $segundo_apellido,$usuario,$telefono,$email)) {
                throw new ModelsException('Todos los datos son necesarios');
            }
    
          $usuario_exist = $this->db->query_select("SELECT * FROM usuario WHERE usuario = '$usuario'");
          if(false!==$usuario_exist && !$edit){
            throw new ModelsException('El usuario ya existe');
          }
    
        }

    final public function crear() {
      try {
        global $http;
                  
        # Controlar errores de entrada en el formulario
        $this->errors();

        # Insertar elementos
        $this->db->query("INSERT INTO sucursal
        (primer_nombre,segundo_nombre,primer_apellido, segundo_apellido, usuario, masculinoRadio, telefono, email)
        VALUES ($this->primer_nombre,$this->segundo_nombre, $this->primer_apellido,$this->segundo_apellido,
        $this->usuario, $this->sexom, $this->telefono, $this->email);");

        return array('success' => 1, 'message' => 'Creado con éxito.');
      } catch(ModelsException $e) {
        return array('success' => 0, 'message' => $e->getMessage());
      }
    }

    final public function editar(){
      try {
            global $http;
            
            # Controlar errores de entrada en el formulario
            $this->errors(true);
    
            # Actualizar elementos
            $this->db->query("UPDATE usuario
            SET primer_nombre = '$primer_nombre', segundo_nombre = '$segundo_nombre', primer_apellido = '$primer_apellido', segundo_apellido = '$segundo_apellido', 
            usuario ='$usuario', sexo ='$sexo', telefono = '$telefono', correo ='$correo'
            WHERE usuario = '$usuario'");
    
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