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
class transaccionesController extends Controllers implements IControllers {

    public function __construct(IRouter $router) {
        parent::__construct($router,array(
          'users_logged' => true
      ));
        
        switch($this->method) {
          case 'compra':
            $this->template->display('transacciones/compra');
          break;
          case 'venta':
            $this->template->display('transacciones/venta');
          break;
          case 'intercambio':
            $this->template->display('transacciones/intercambio');
          break;
          default:
           $this->template->display('transacciones/transacciones');
          break;
        }
    }
}