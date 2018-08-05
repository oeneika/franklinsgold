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

/**
 * Controlador usado por los clientes para crear ordenes/
*/
class ordenclienteController extends Controllers implements IControllers {

    public function __construct(IRouter $router) {
        parent::__construct($router,array(
            'users_logged' => true,
            'users_clienteadmin' => true
        ));

        $s = new Model\Sucursales($router);
        $o = new Model\Orden($router);
        $m = new Model\Monedas($router);

        $id_owner = $this->user["id_user"];
        $this->template->display('ordenes/dashboard',array(
            'sucursales' => $s->get(),
            'ultimas_cinco_ordenes_oro' => $o->get("orden.estado=2 and orden.tipo_gramo='oro' and u.id_user='$id_owner'",5),
            'ultimas_cinco_ordenes_plata' => $o->get("orden.estado=2 and orden.tipo_gramo='plata' and u.id_user='$id_owner'",5),
            'total_oro_comprado' => $o->getTotalGramos("oro","id_usuario='$id_owner'"),
            'total_plata_comprado' => $o->getTotalGramos("plata","id_usuario='$id_owner'"),
            'ultimo_precio_oro' => ($m->getPrice("oro"))[0][0],
            'ultimo_precio_plata' => ($m->getPrice("plata"))[0][0],
        ));
 
        
    }
}