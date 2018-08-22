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
    private $id_sucursal;
    private $id_comercio;

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
        $this->id_sucursal = $http->request->get('id_sucursal');
        $this->id_comercio = $http->request->get('id_comercio');
        

        # Verificar que no están vacíos
        if (Helper\Functions::e(
            $this->diametro,$this->espesor,$this->composicion,
            $this->peso,$this->id_origen
        )) {
            throw new ModelsException('Todos los campos marcados con "*" son necesarios.');
        }

        if($this->diametro<0.1 or $this->espesor<0.1 or $this->peso<0.1 ){
            throw new ModelsException('Debe introducir características válidas.');
        }

        if( $this->composicion != "oro" and $this->composicion != "plata" ){
            throw new ModelsException('La composición es incorrecta.');
        }

        if(Helper\Functions::emp($this->id_sucursal) && !$edit){
            throw new ModelsException('Debe seleccionar una sucursal.');
        }

    }

    /**
     * Agrega usuarios 
     * 
     * @return array
    */ 
    public function add() : array {
        try {
            global $http, $config;

            #Revisa errores del formulario
            $this->errors();

            $fecha = time();
        
            $u = array(
            'fecha_elaboracion' => $fecha,
            'diametro' => $this->diametro,
            'espesor' => $this->espesor,
            'composicion' => $this->composicion,
            'peso' => $this->peso,
            'id_origen' => $this->id_origen
            );


            # Obtenemos el id de la moneda insertada
            $id_moneda =  $this->db->insert('moneda',$u);

            $id_user = $this->db->select('id_user','sucursal',null,"id_sucursal = $this->id_sucursal")[0]['id_user'];
            $this->db->insert('user_moneda', array(
                'id_usuario'=>$id_user,
                'codigo_moneda'=> $id_moneda
            ));

            $fecha = date('dmY',$fecha);

            # Datos del codigo QR
            $origen = $this->db->select('abreviatura','origen',null,"id_origen = $this->id_origen")[0];
            $this->diametro = str_pad($this->diametro,3,'0',STR_PAD_LEFT);
            $this->espesor = str_pad($this->espesor,3,'0',STR_PAD_LEFT);
            $this->composicion = $this->composicion == 'oro' ? 'ORO':'PLA';
            $this->peso = str_pad($this->peso,3,'0',STR_PAD_LEFT);
            $id_moneda_padded = str_pad($id_moneda,6,'0',STR_PAD_LEFT);

            #Concatena una palabra para evitar repeticiones del codigoqr
            $conc = $origen['abreviatura'] . " $id_moneda_padded $this->diametro $this->espesor $this->composicion $this->peso $fecha";

            # Url del codigo qr
            $url = "https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=".urlencode($conc);

            # Ruta en la que se guardara la imagen
            $img = "../views/img/codigos/monedas/$id_moneda.png";
            file_put_contents($img, file_get_contents($url));

            #Se actualiza la db con la ruta de la imagen
            $this->db->update('moneda',array(
                'codigo_qr'=> $config['build']['url'] . "/views/img/codigos/monedas/$id_moneda.png",
                'qr_alfanumerico'=> $conc
            ), "codigo = '$id_moneda'");

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

            $fecha = $this->db->select('fecha_elaboracion','moneda',null,"codigo = $id");
            $fecha = $fecha[0]['fecha_elaboracion'];

            $this->errors(true);

            $u = array(
                'diametro' => $this->diametro,
                'espesor' => $this->espesor,
                'composicion' => $this->composicion,
                'peso' => $this->peso,
                'id_origen' => $this->id_origen
            );

            $this->diametro = str_pad($this->diametro,3,'0',STR_PAD_LEFT);
            $this->espesor = str_pad($this->espesor,3,'0',STR_PAD_LEFT);
            $this->composicion = $this->composicion == 'oro' ? 'ORO':'PLA';
            $this->peso = str_pad($this->peso,3,'0',STR_PAD_LEFT);
            $id_moneda_padded = str_pad($id,6,'0',STR_PAD_LEFT);

            $fecha = date('dmY',$fecha);

            $origen = $this->db->select('abreviatura','origen',null,"id_origen = $this->id_origen")[0];

            #Concatena una palabra para evitar repeticiones del codigoqr
            $conc = $origen['abreviatura'] . " $id_moneda_padded $this->diametro $this->espesor $this->composicion $this->peso $fecha";

            #Se le agrega el codigo al array
            $u['qr_alfanumerico'] = $conc;

            # Url del codigo qr
            $url = "https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=".urlencode($conc);

            # Ruta en la que se guardara la imagen
            $img = "../views/img/codigos/monedas/$id.png";
            file_put_contents($img, file_get_contents($url));


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
     * Obtiene elementos de monedas
     *   
     * @param select :  elementos a traer en la consulta
     * @param sucursal  : inner join con id sucursal
     *
     * @return false|array con información de los usuarios
     */  
    public function getMonedas(string $select = '*',string $sucursal=" ") {
        $inner = "INNER JOIN origen ON origen.id_origen=moneda.id_origen ";
        $inner = $inner . $sucursal;
        return $this->db->select('moneda.*,origen.nombre','moneda',$inner);
    }


    /**
     * Obtiene los ultimos precios del oro y de la plata
     * 
     *  @param composicion :  composicion del material (oro o plata)
     *      
     */
    public function getPrice(string $composicion="oro"){

        $composicion= $composicion == 'oro' ? 'gold' : 'silver';
        $url = 'https://goldiraguide.org/wp-admin/admin-ajax.php';
    
        #Se procede a hacer la peticion a la api
        $opt = array(
            CURLOPT_POST =>true,
            CURLOPT_RETURNTRANSFER =>true,
            CURLOPT_POSTFIELDS =>['action' => 'getMetalPrice', 'api_key' => 'anonymous'],
            CURLOPT_URL => $url
        );
        $ch = curl_init();
        curl_setopt_array($ch,$opt);
        $result = curl_exec($ch);     
        curl_close($ch);    
        $result = json_decode($result, true);
        $data = $result['buttonFrame'][$composicion]['1m']['data'];
      
            if($data != null){
                return [array_reverse($data)];
            }

             return array(array(0));

    }


    /**
     * Obtiene las ultimas fechas de act del oro y de la plata
     * 
     *  @param composicion :  composicion del material (oro o plata)
     *      
     */
    public function getDate(string $composicion="oro"){

        $composicion= $composicion == 'oro' ? 'gold' : 'silver';
        $url = 'https://goldiraguide.org/wp-admin/admin-ajax.php';
    
        #Se procede a hacer la peticion a la api
        $opt = array(
            CURLOPT_POST =>true,
            CURLOPT_RETURNTRANSFER =>true,
            CURLOPT_POSTFIELDS =>['action' => 'getMetalPrice', 'api_key' => 'anonymous'],
            CURLOPT_URL => $url
        );
        $ch = curl_init();
        curl_setopt_array($ch,$opt);
        $result = curl_exec($ch);     
        curl_close($ch);    
        $result = json_decode($result, true);
        $data = $result['buttonFrame'][$composicion]['1m']['labels'];

        return [array_reverse($data)];

    }

    /**
     * Devuelve un array con datos actualizados del oro/plata
     */
    public function getfulldata(){

        $url = 'https://goldiraguide.org/wp-admin/admin-ajax.php';
    
        #Se procede a hacer la peticion a la api
        $opt = array(
            CURLOPT_POST =>true,
            CURLOPT_RETURNTRANSFER =>true,
            CURLOPT_POSTFIELDS =>['action' => 'getMetalPrice', 'api_key' => 'anonymous'],
            CURLOPT_URL => $url
        );
        $ch = curl_init();
        curl_setopt_array($ch,$opt);
        $result = curl_exec($ch);     
        curl_close($ch);    
        $result = json_decode($result, true);

        $preciosOro = array_reverse($result['buttonFrame']['gold']['1m']['data']);
        $fechasOro = array_reverse($result['buttonFrame']['gold']['1m']['labels']);

        $preciosPlata = array_reverse($result['buttonFrame']['silver']['1m']['data']);
        $fechasPlata = array_reverse($result['buttonFrame']['silver']['1m']['labels']);

        return array(
             'oro' => array ('fechas' => $fechasOro,
                             'precios' => $preciosOro),
             'plata' => array ('fechas' => $fechasPlata,
                               'precios' => $preciosPlata)
         );
 
     }

    /**
     * Trae los datos generales relacionados a las monedas
     */
    public function datosGenerales(){

        $ultimo_precio_oro = ($this->getPrice("oro"))[0][0];
        $ultimo_precio_plata = ($this->getPrice("plata"))[0][0];

        $monedas_oro=$this->db->select('diametro,espesor,peso','moneda',null,"composicion='oro'");
        $monedas_plata=$this->db->select('diametro,espesor,peso','moneda',null,"composicion='plata'");
        
        $total_monedas_oro = false !== $monedas_oro ? sizeof($monedas_oro):0;
        $total_monedas_plata = false !== $monedas_plata ? sizeof($monedas_plata):0;

        $total_oro_dolares = 0;
        for ($i=0; $i < $total_monedas_oro ; $i++) { 
            
            $total_oro_dolares =$total_oro_dolares + ( $monedas_oro[$i]["peso"] * ($ultimo_precio_oro/28.3495) ); 

        }

        $total_plata_dolares = 0;
        for ($i=0; $i < $total_monedas_plata ; $i++) { 

            $total_plata_dolares =$total_plata_dolares + ( $monedas_plata[$i]["peso"] * ($ultimo_precio_plata/28.3495) );
            
        }

        return array(
            'total_monedas_oro' => $total_monedas_oro,
            'total_monedas_plata' => $total_monedas_plata,
            'total_oro_discriminado' => $total_oro_dolares,
            'total_plata_discriminado' => $total_plata_dolares,
            'balance_general' => $total_oro_dolares + $total_plata_dolares
        );



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