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
 * Controlador usuarios/
 *
 * @author Ocrend Software C.A <bnarvaez@ocrend.com>
*/
class usuariosController extends Controllers implements IControllers {

    public function __construct(IRouter $router) {

        parent::__construct($router,array(
            'users_logged' => true,
            'users_admin'=>true
        ));
        
        $u = new Model\Users($router);  
        $s = new Model\Sucursales($router);  
        $a = new Model\Afiliados($router);  

        switch($this->method) {
            case 'eliminar':
                $u->del();
            break;
            default:
            $this->template->display('usuarios/usuarios',array(
                'usuarios' => $u->getUsers(),
                'afiliados' => $a->get(),
                'sucursales' => $s->get()
            ));
            break;
        }


    }
}