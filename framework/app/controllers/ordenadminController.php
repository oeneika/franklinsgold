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
 * Controlador usado por los admis para gestionar ordenes/
*/
class ordenadminController extends Controllers implements IControllers {

    public function __construct(IRouter $router) {        
        parent::__construct($router,array(
            'users_logged' => true,
            'users_vendedorsupervisoradmin'=>true
        ));

        $o = new Model\Orden($router);  
        $u = new Model\Users($router);  

        switch($this->method) {
            case 'eliminar':
                $o->del();           
            break;
            case 'confirmar':
                $o->confirmOrden();           
            break;
            case 'concretar':
                $o->specifyOrden();           
            break;
            default:
            $select = "orden.*,u.primer_nombre,u.primer_apellido,u.numero_cuenta,u.nombre_banco";
            $this->template->display('ordenes/ordenes',array(
                'ordenes' => $o->get($select),
                'clientes' => $u->getUsers('*','users.tipo=2 or (tipo=1 and es_comercio_afiliado=1)')
            ));
            break;
        }
 
        
    }
}