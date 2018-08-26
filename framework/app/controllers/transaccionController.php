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
 * Controlador transacciones/
 *
 * @author Ocrend Software C.A <bnarvaez@ocrend.com>
*/
class transaccionController extends Controllers implements IControllers {

    public function __construct(IRouter $router) {
      parent::__construct($router,array(
        'users_logged' => true,
        'users_vendedoradmin' => true
      ));

        $t = new Model\Transaccion($router); 
        $u = new Model\Users($router); 
        $m = new Model\Monedas($router);  
        
        switch($this->method) {
          case 'compra':
            $this->template->display('transaccion/compra',array(
                'transacciones' => $t->getTransacciones(1),
                'usuarios' => $u->getUsers("*","tipo!=0"),
                'monedas' => $m->getMonedas()
            ));
          break;
          case 'transaccion_en_espera':
          $this->template->display('transaccion/transaccion_en_espera',array(
            'transacciones_en_espera' => $t->getTransaccionesEnEspera()
          ));
          break;
          default:
          $this->template->display('transaccion/compra',array(
            'transacciones' => $t->getTransacciones(1),
            'usuarios' => $u->getUsers("*","tipo!=0"),
            'monedas' => $m->getMonedas()
          ));
          break;
        }
    }
}