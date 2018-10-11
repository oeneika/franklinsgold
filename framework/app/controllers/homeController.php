<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace app\controllers;

use app\models as Model;
use Ocrend\Kernel\Helpers as Helper;
use Ocrend\Kernel\Controllers\Controllers;
use Ocrend\Kernel\Controllers\IControllers;
use Ocrend\Kernel\Router\IRouter;
use Ocrend\Kernel\Helpers\Functions;

/**
 * Controlador home/
 *
 * @author Ocrend Software C.A <bnarvaez@ocrend.com>
*/
class homeController extends Controllers implements IControllers {

    public function __construct(IRouter $router) {
        parent::__construct($router,array(
            'users_logged' => true
        ));
      
        $d = new Model\Dashboard;
        $o = new Model\Orden;
        $u = new Model\Users;
        $di = new Model\Divisa;

        #Variable que contendrá las ordenes a pedir segun el usuario logeado
        $ordenes = false;

        #Si el usuario logeado es un supervisor de un comercio afiliado      
        if($this->user["tipo"] == 3 and $this->user["id_comercio_afiliado"]!=null){
            $ordenes = $o->getOrdenesComerciosAfiliados($this->user["id_comercio_afiliado"]);
        }

        #Si el usuario logeado es un vendedor de un comercio afiliado      
        if($this->user["tipo"] == 1 and $this->user["id_comercio_afiliado"]!=null){
            $ordenes = $o->getOrdenesComerciosAfiliados(0,$this->user["id_user"]);
        }

        #Si el usuario logeado es un admin
        $cantidad_compras_por_comercio = false;
        $ordenes_descuentos_comercios = false;
        $cantidades_gramos_comercios = false;
        if($this->user["tipo"] == 0 ){

            $ordenes = $o->getOrdenesComerciosAfiliados();
            $cantidad_compras_por_comercio = $o->getCantOrdenesComerciosAfiliados(); 
            $ordenes_descuentos_comercios = $o->getOrdenesDescuentosComerciosAfiliados();          
            $cantidades_gramos_comercios = $o->getGramosComerciosAfiliados();    
        }

        $this->template->display('home/home',array(
            'data'=> $d->getData(),
            'clientes'=> $u->getUsers('*','tipo=2'),
            'ordenes' => $ordenes,
            'cantidad_compras_por_comercio' => $cantidad_compras_por_comercio,
            'cantidades_gramos_comercios' => $cantidades_gramos_comercios,
            'ordenes_descuentos_comercios' => $ordenes_descuentos_comercios,
            'precio_bolivar' => ($di->getDivisas("precio_dolares","nombre_divisa='Bolívar Soberano'"))[0]["precio_dolares"]
        ));
    }
}