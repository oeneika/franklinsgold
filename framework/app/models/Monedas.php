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
use Ocrend\Kernel\Helpers\phpqrcore\qrlib;

/**
 * Modelo Monedas
 */
class Monedas extends Models implements IModels {
    use DBModel;

    private $diametro;
    private $espesor;
    private $composicion;
    private $peso;
    private $id_origen;

    /**
     * Revisa errores en el formulario
     * 
     * @return exception
    */ 
    private function errors(bool $edit = false){

        global $http;

        $this->diametro = $http->request->get('diametro');
        $this->espesor = $http->request->get('espesor');
        $this->composicion = $http->request->get('composicion');       
        $this->peso = $http->request->get('peso');
        $this->id_origen = $http->request->get('id_origen');
        

        # Verificar que no están vacíos
        if (Helper\Functions::e(
            $this->diametro,$this->espesor,$this->composicion,
            $this->peso,$this->id_origen
        )) {
            throw new ModelsException('Todos los campos marcados con "*" son necesarios.');
        }

        if($this->diametro<0 or $this->espesor<0 or $this->composicion<0 or $this->peso<0 ){
            throw new ModelsException('Debe introducir características válidas.');
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
        
            $u = array(
            'fecha_elaboracion' => time(),
            'diametro' => $this->diametro,
            'espesor' => $this->espesor,
            'composicion' => $this->composicion,
            'peso' => $this->peso,
            'id_origen' => $this->id_origen
            );

            # Registrar al usuario
            $id_user =  $this->db->insert('moneda',$u);

            return array('success' => 1, 'message' => 'Moneda creada con éxito!');
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

            $id = $http->request->get('codigo');

            $this->errors(true);

            $u = array(
                'diametro' => $this->diametro,
                'espesor' => $this->espesor,
                'composicion' => $this->composicion,
                'peso' => $this->peso,
                'id_origen' => $this->id_origen
            );


            #Array con datos validos para el update
            $data = array();

            #Valida que los datos no esten vacios y los inserta en el array "data"
            foreach ($u as $key=>$val) {
                if(NULL !== $u[$key] && !Functions::emp($u[$key])){
                    $data[$key] = $u[$key];
                }
            }


            #Edita una moneda
            $this->db->update('moneda',$data,"codigo = '$id'",'1');

            return array('success' => 1, 'message' => 'Moneda editada con éxito!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }


    /**
     * Obtiene a todas las monedas
     *    
     *
     * @return false|array con información de los usuarios
     */  
    public function getMonedas(string $select = '*') {
        $inner = "INNER JOIN origen ON origen.id_origen=moneda.id_origen";
        return $this->db->select('moneda.*,origen.nombre','moneda',$inner);
    }


    /**
     * Eliminar usuario
    */
    final public function del() {
        Global $config;

       $res = $this->db->delete('moneda',"codigo='$this->id'",'1');

      # Redireccionar al controlador usuarios con un success=true
      Functions::redir($config['build']['url'] . 'monedas/&success=true');

    }

    /**
     * __construct()
    */
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
        $this->startDBConexion();
    }
}