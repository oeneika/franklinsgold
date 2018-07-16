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
 * Modelo Afiliados
 */
class Afiliados extends Models implements IModels {
    use DBModel;

    /**
     * Array en el que se guardaran datos del comercio
     */
    private $data;

    /**
     * Errores en telefonos
     */
    private function errorsTel(){
        if (!array_key_exists('telefono',$this->data)) {
            throw new ModelsException('Campo telefono no definido');
        }
        foreach ($this->data['telefono'] as $key => $value) {

            if (Helper\Functions::emp($value)){
                throw new ModelsException('uno o mas telefonos estan vacios');
            }

            if (strlen($value) < 11){
                throw new ModelsException("Telefono invalido, debe tener al menos 11 digitos");              
            }

        }
    }

    /**
     * Funcion para verificar errores en el formulario
     */
    private function errors($edit = false){
        global $http;
        $this->data = $http->request->all();

        if (!array_key_exists('nombre',$this->data) || Helper\Functions::emp($this->data['nombre'])) {
            throw new ModelsException('El nombre no debe estar vacio');
        }

        if (!array_key_exists('sucursal',$this->data)  || Helper\Functions::emp($this->data['sucursal']) ) {
            throw new ModelsException('La sucursal no debe estar vacia');
        }

        if (!array_key_exists('direccion',$this->data)  || Helper\Functions::emp($this->data['direccion']) ) {
            throw new ModelsException('La direccion no debe estar vacia');
        }

        $this->errorsTel();
    }

    /**
     * Inserta telefonos
     */
    private function addTelefonos($id_afiliado){

        foreach ($this->data['telefono'] as $key => $value) {
            $this->db->insert('telefono',array(
                'id_comercio_afiliado' => $id_afiliado,
                'telefono'=> $value
            ));
        }

    }

    /**
     * Crear comercio
     * 
     * @return array
    */ 
    public function add() : array {
        try {

            $this->errors();

            $id = $this->db->insert('comercio_afiliado', array(
                'nombre' => $this->data['nombre'],
                'sucursal' => $this->data['sucursal'],
                'direccion' => $this->data['direccion']
            ));

            $this->addTelefonos($id);

            return array('success' => 1, 'message' => 'Comercio creado con exito!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }

    /**
     * Crear un intercambio
     * 
     * @return array
    */ 
    public function addIntercambio() : array {
        try {
            global $http;

            #Data POST del formulario
            $data_form = $http->request->all();

            #Se buscan los datos de la moneda en la DB
            $data_moneda = $this->db->select('composicion,peso','moneda',null,'codigo = '. $data_form['moneda']);
            
            #Se pregunta si la composicion es oro o plata
            $url = $data_moneda[0]['composicion'] == 'oro'? 'https://www.quandl.com/api/v3/datasets/LBMA/GOLD.json?api_key=CPE8TFT3Z18GjsP3C9pV' : 'https://www.quandl.com/api/v3/datasets/LBMA/SILVER.json?api_key=CPE8TFT3Z18GjsP3C9pV';
    
            #Se procede a hacer la peticion a la api
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $result = curl_exec($ch);     
            curl_close($ch);
                   
            $obj = json_decode($result);
            $dataset = $obj->{'dataset'};
            $data = $dataset->{'data'};

            #Se calcula el valor de la moneda
            $precio_dolares = $data_moneda[0]['peso'] * ($data[0][1]/28.3495);


            #Se inserta en la DB
            $id = $this->db->insert('afiliado_moneda', array(
                'codigo' => $data_form['moneda'],
                'id_comercio_afiliado' => $data_form['id_comercio_afiliado'],
                'monto' => $precio_dolares,
                'fecha' => time(),
            ));


            return array('success' => 1, 'message' => 'Intercambio creado con exito!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }

    /**
     * Edita un comercio
     * 
     * @return array
     */
    public function edit() : array {
        try {

            $this->errors(true);

            $id = $this->data['id_comercio_afiliado'];
            $this->db->update('comercio_afiliado', array(
                'nombre' => $this->data['nombre'],
                'sucursal' => $this->data['sucursal'],
                'direccion' => $this->data['direccion']
            ),"id_comercio_afiliado = $id");

            $this->db->delete('telefono',"id_comercio_afiliado=$id");

            $this->addTelefonos($id);

            return array('success' => 1, 'message' => 'Comercio editado con exito!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }

    /**
     * Trae todos los comercios en la db
     */
    public function get($select = '*'){
        return $this->db->select($select,'comercio_afiliado');
    }

    /**
     * Trae todos los telefonos de un comercio en la db
     */
    public function getTelefonos($id){
        return $this->db->select('telefono','telefono',null,"id_comercio_afiliado = $id");
    }

    /**
     * Trae intercambios  de la DB
     */
    public function getIntercambios($id){
        $where = "afiliado_moneda.id_comercio_afiliado = $id";
        $intercambios = $this->db->select('codigo, monto, fecha','afiliado_moneda',null, $where);
        $total = $this->db->select('SUM(monto) AS total','afiliado_moneda',null, $where);
        return array(
            'intercambios' => $intercambios,
            'total' => $total
        );
    }

    /**
     * Elimina comercio afiliado
     */
    public function del(){
        global $config;
        $d= $this->db->delete('comercio_afiliado',"id_comercio_afiliado = $this->id");
        Helper\Functions::redir($config['build']['url'] . "afiliados/&success=true");
    }


    /**
     * __construct()
    */
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
		$this->startDBConexion();
    }
}