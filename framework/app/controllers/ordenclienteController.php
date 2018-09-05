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
        $d = new Model\Divisa($router);
        
        $id_owner = $this->user["id_user"];

        $select = "orden.cantidad,orden.precio,orden.tipo_orden,orden.precio,orden.fecha,u.primer_nombre,u.primer_apellido";
        $where_oro = "orden.estado=4 and orden.tipo_gramo='oro' and u.id_user='$id_owner'";
        $where_plata = "orden.estado=4 and orden.tipo_gramo='plata' and u.id_user='$id_owner'";
        $precio_bolivar = ($d->getDivisas("precio_dolares","nombre_divisa='Bolívar Soberano'"))[0]["precio_dolares"];

        switch($this->method) {
          case 'compraoro':
            $this->template->display('ordenes/compraoro',array(
                'precio_bolivar' => $precio_bolivar
            ));
          break;
          case 'compraplata':
            $this->template->display('ordenes/compraplata',array(
                'precio_bolivar' => $precio_bolivar
            ));
          break;
          case 'ventaoro':
            $this->template->display('ordenes/ventaoro',array(
                'precio_bolivar' => $precio_bolivar
            ));
          break;
          case 'ventaplata':
            $this->template->display('ordenes/ventaplata',array(
                'precio_bolivar' => $precio_bolivar
            ));
          break;
          default:
            $this->template->display('ordenes/dashboard',array(
            'sucursales' => $s->get(),
            'ultimas_cinco_ordenes_oro' => $o->get($select,$where_oro,5,"ORDER BY orden.id_orden DESC"),
            'ultimas_cinco_ordenes_plata' => $o->get($select,$where_plata,5,"ORDER BY orden.id_orden DESC"),
            'total_oro_comprado' => $o->getTotalGramos("oro","id_usuario='$id_owner'"),
            'total_plata_comprado' => $o->getTotalGramos("plata","id_usuario='$id_owner'"),
            'precio_bolivar' => $precio_bolivar
        ));
          break;
        }
        
 
        
    }
}