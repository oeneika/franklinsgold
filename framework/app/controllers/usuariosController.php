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
        parent::__construct($router);
        $u = new Model\Usuarios($router); //Se le pasa el router para saber el id del elemento a editar

        switch ($this->method) {
        	case 'crear':
        		$u ->crear();
        		break;
        	case 'editar':
        		$u -> editar();
        		break;
        	case 'eliminar':
        		$u -> eliminar();
        		break;
        	default:
        		$this->template->display('usuarios/usuarios',array(
        		'usuario' => $u->get()
        		));
        		break;
        }
        
    }
}