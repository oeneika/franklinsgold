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
 * Controlador monedas/
 *
 * @author Ocrend Software C.A <bnarvaez@ocrend.com>
*/
class monedasController extends Controllers implements IControllers {

    public function __construct(IRouter $router) {
        parent::__construct($router,array(
            'users_logged' => true,
            'users_admin'=>true
        ));
        
        $m = new Model\Monedas($router);  
        $o = new Model\Origen($router);   

        switch($this->method) {
            case 'eliminar':
                $m->del();           
            break;
            default:
            $this->template->display('monedas/monedas',array(
                'monedas' => $m->getMonedas(),
                'origenes' => $o->getOrigenes()
            ));
            break;
        }
    }
}