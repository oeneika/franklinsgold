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
class dashBoardpublicoController extends Controllers implements IControllers {

    public function __construct(IRouter $router) {
        parent::__construct($router);

        $o = new Model\Orden($router);
        $m = new Model\Monedas($router);
     
        $select = "orden.fecha,orden.cantidad,orden.precio,u.primer_nombre,u.primer_apellido,u.numero_cuenta";
        $this->template->display('dashboardpublico/dashboardpublico',array(
            'compras_oro' => $o->get($select,"orden.tipo_orden=1 and orden.estado=4 and orden.tipo_gramo='oro'",null,"ORDER BY orden.id_orden DESC"),
            'compras_plata' => $o->get($select,"orden.tipo_orden=1 and orden.estado=4 and orden.tipo_gramo='plata'",null,"ORDER BY orden.id_orden DESC"),
            'ventas_oro' => $o->get($select,"orden.tipo_orden=2 and orden.estado=4 and orden.tipo_gramo='oro'",null,"ORDER BY orden.id_orden DESC"),
            'ventas_plata' => $o->get($select,"orden.tipo_orden=2 and orden.estado=4 and orden.tipo_gramo='plata'",null,"ORDER BY orden.id_orden DESC"),
            'ultimo_precio_oro' => ($m->getPrice("oro"))[0][0],
            'ultimo_precio_plata' => ($m->getPrice("plata"))[0][0],

            'compras_oro_1dia' => ($o->getVolumenes("-1 days","oro",1))[0]["volumen"],
            'compras_oro_1mes' => ($o->getVolumenes("-31 days","oro",1))[0]["volumen"],
            'ventas_oro_1dia' => ($o->getVolumenes("-1 days","oro",2))[0]["volumen"],
            'ventas_oro_1mes' => ($o->getVolumenes("-31 days","oro",2))[0]["volumen"],

            'compras_plata_1dia' => ($o->getVolumenes("-1 days","plata",1))[0]["volumen"],
            'compras_plata_1mes' => ($o->getVolumenes("-31 days","plata",1))[0]["volumen"],
            'ventas_plata_1dia' => ($o->getVolumenes("-1 days","plata",2))[0]["volumen"],
            'ventas_plata_1mes' => ($o->getVolumenes("-31 days","plata",2))[0]["volumen"]
        ));

        
    }
}