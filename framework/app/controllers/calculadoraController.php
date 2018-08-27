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
 * Controlador calculadora/
*/
class calculadoraController extends Controllers implements IControllers {

    public function __construct(IRouter $router) {
        parent::__construct($router,array(
            'users_logged' => true,
            'users_clienteadmin' => true,          
            'users_vendedoradmin'=> false,
            'users_supervisoradmin'=> false
        ));
        
        $d = new Model\Divisa();
        $m = new Model\Monedas();
        
		$this->template->display('calculadora/calculadora',array(
            'divisas' => $d->getDivisas(),
            'ultimo_precio_oro' => ($m->getPrice("oro"))[0][0],
            'ultimo_precio_plata' => ($m->getPrice("plata"))[0][0]
        ));
    }
}