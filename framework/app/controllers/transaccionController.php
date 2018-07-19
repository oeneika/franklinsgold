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
        'users_logged' => true
      ));

        $t = new Model\Transaccion($router); 
        $u = new Model\Users($router); 
        $m = new Model\Monedas($router); 
        $s = new Model\Sucursales($router);  
        $a = new Model\Afiliados($router);  

        //$t->calculatePrice(59);

        switch($this->method) {
          case 'compra':
            $this->template->display('transaccion/compra',array(
                'transacciones' => $t->getTransacciones(1),
                'usuarios' => $u->getUsers("*","tipo!=0"),
                'monedas' => $m->getMonedas(),
                'sucursales' => $s->get(),
                'afiliados' => $a->get()
            ));
          break;
          case 'venta':
            $this->template->display('transaccion/venta',array(
                'transacciones' => $t->getTransacciones(2),
                'usuarios' => $u->getUsers("*","tipo=2"),
                'monedas' => $m->getMonedas(),
                'sucursales' => $s->get(),
                'afiliados' => $a->get()
            ));
          break;
          case 'intercambio':
            $this->template->display('transaccion/intercambio',array(
                'transacciones' => $t->getTransacciones(3),
                'usuarios' => $u->getUsers("*","tipo=2"),
                'monedas' => $m->getMonedas()
            ));
          break;
          case 'intercambioafiliado':
            $this->template->display('transaccion/intercambioafiliado',array(
              'transacciones' => $t->getIntercambiosAfiliados(),
              'usuarios' => $u->getUsers("*","tipo=2"),
              'monedas' => $m->getMonedas(),
              'afiliados' => $a->get()
          ));
          break;
          default:
           $this->template->display('transaccion/compra',array(
                'transacciones' => $t->getTransacciones(1),
                'usuarios' => $t->getUsers("*","tipo=2"),
                'monedas' => $t->getMonedas(),
                'sucursales' => $s->get(),
                'afiliados' => $a->get()
            ));
          break;
        }
    }
}