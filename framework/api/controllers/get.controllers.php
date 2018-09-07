<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

use app\models as Model;

$app->get('/', function() use($app) {
    return $app->json(array()); 
});

$app->get('origenes/get', function() use($app) {
    $o = new Model\Origen; 

    return $app->json($o->getOrigenes());
});

$app->get('/transaccion/get/{id}', function($id) use($app) {
    $t = new Model\Transaccion; 
  
    return $app->json($t->getByUser($id));   
  });

$app->get('afiliados/getTelefonos/{id}', function($id) use($app) {
    $a = new Model\Afiliados; 

    return $app->json($a->getTelefonos($id));
});

$app->get('afiliados/getIntercambios/{id}', function($id) use($app) {
    $a = new Model\Afiliados; 

    return $app->json($a->getIntercambios($id));
});

$app->get('afiliados/getIntercambiosUser/{id}/{id_comercio}', function($id,$id_comercio) use($app) {
    $t = new Model\Transaccion; 

    return $app->json($t->getIntercambiosUsers($id,$id_comercio));
});

$app->get('/get/datos_generales', function() use($app) {
    $m = new Model\Monedas; 
  
    return $app->json($m->datosGenerales());   
});

$app->get('/get/datos_generales_usuario', function() use($app) {
    $u = new Model\Users; 
  
    return $app->json($u->datosGenerales());   
});

$app->get('/get/transaccion_en_espera/{id}', function($id) use($app) {
    $t = new Model\Transaccion; 
  
    return $app->json($t->getTransaccionEnEspera($id));   
});

$app->get('/get/monedas/BySucursal/{id}', function($id) use($app) {
    $m = new Model\Monedas; 
  
    $inner = "INNER JOIN sucursal s ON s.id_sucursal='$id'
              INNER JOIN user_moneda um ON um.id_usuario=s.id_user and um.codigo_moneda=moneda.codigo";

    return $app->json($m->getMonedas('*',$inner));   
});


  $app->get('/get/oro', function() use($app) {
    $m = new Model\Monedas; 
  
    return $app->json($m->getPrice("oro"));   
  });

  $app->get('/get/plata', function() use($app) {
    $m = new Model\Monedas; 
  
    return $app->json($m->getPrice("plata"));   
  });

  $app->get('/getdate/oro', function() use($app) {
    $m = new Model\Monedas; 
  
    return $app->json($m->getDate("oro"));   
  });

  $app->get('/getdate/plata', function() use($app) {
    $m = new Model\Monedas; 
  
    return $app->json($m->getDate("plata"));   
  });

/**
 * Servicio que devuelve datos de los precios de oro y plata
 */
$app->get('/getfulldata', function() use($app) {
    $m = new Model\Monedas; 

    return $app->json($m->getFullData());   
});

/**
 * Servicio que devuelve las ultimas cinco ordenes concretadas
 *
 * @return json
*/
$app->get('/orden/historial', function() use($app) {
    $o = new Model\Orden; 

    $select = "orden.tipo_orden,orden.cantidad,orden.tipo_gramo,orden.precio";
    $where = "orden.estado=4";
    return $app->json($o->get($select,$where,5));      
});

/**
 * Servicio que devuelve las ordenes concretadas por usuario
 *
 * @return json
*/
$app->get('/orden/get/{id}', function($id) use($app) {
    $o = new Model\Orden; 
  
    $select = "orden.tipo_orden,orden.cantidad,orden.tipo_gramo,orden.precio,orden.fecha";
    $where = "orden.estado=4 and u.id_user='$id'";
    return $app->json($o->get($select,$where));   
});

/**
 * Servicio que devuelve gramos de oro comprado
 *
 * @return json
*/
$app->get('/terminosycondiciones/get', function() use($app) {
    $r = new Model\Registro; 

    return $app->json($r->getTerminos());      
});

/**
 * Servicio que devuelve el precio del BsS, oro y plata almacenado en la base de datos
 *
 * @return json
*/
$app->get('/get/preciosDivisas', function() use($app) {
    $d = new Model\Divisa; 
    $o = new Model\Orden; 

    #Precios actuales de las divisas
    $oro = $d->getDivisas("precio_dolares,precio_dolares_venta","nombre_divisa='Oro Franklin'")[0];
    $plata = $d->getDivisas("precio_dolares,precio_dolares_venta","nombre_divisa='Plata Franklin'")[0];
    $bss = $d->getDivisas("precio_dolares,precio_dolares_venta","nombre_divisa='Bolívar Soberano'")[0];

    #Ultimas ordenes de compra/venta de oro/plata
    $ultimop_oro_compra = $o->get("precio","tipo_gramo='oro' and tipo_orden=1 and estado=4",1,"ORDER BY id_orden")[0]["precio"];
    $ultimop_oro_venta = $o->get("precio","tipo_gramo='oro' and tipo_orden=2 and estado=4",1,"ORDER BY id_orden")[0]["precio"];
    $ultimop_plata_compra = $o->get("precio","tipo_gramo='plata' and tipo_orden=1 and estado=4",1,"ORDER BY id_orden")[0]["precio"];
    $ultimop_plata_venta = $o->get("precio","tipo_gramo='plata' and tipo_orden=2 and estado=4",1,"ORDER BY id_orden")[0]["precio"];

    #Configura el estado de los precios, 0:disminuyo ,1:aumento, 2:se mantuvo
    $estado_oro_compra = $ultimop_oro_compra > $oro["precio_dolares"] ? 0 : $ultimop_oro_compra < $oro["precio_dolares"] ? 1 : 2;
    $estado_oro_venta = $ultimop_oro_venta > $oro["precio_dolares_venta"] ? 0 : $ultimop_oro_venta < $oro["precio_dolares_venta"] ? 1 : 2;
    $estado_plata_compra = $ultimop_plata_compra > $plata["precio_dolares"] ? 0 : $ultimop_plata_compra < $plata["precio_dolares"] ? 1 : 2;
    $estado_plata_venta = $ultimop_plata_venta > $plata["precio_dolares_venta"] ? 0 : $ultimop_plata_venta < $plata["precio_dolares_venta"] ? 1 : 2;

    $a = array (
        'oro' => array(
            'precios'=>$oro,
            'estados'=>array(
            'estado_compra'=>$estado_oro_compra,
            'estado_venta'=>$estado_oro_venta)
        ),
        'plata' =>array(
            'precios'=>$plata,
            'estados'=>array(
            'estado_compra'=>$estado_plata_compra,
            'estado_venta'=>$estado_plata_venta)
        ),
        'bss' =>array(
            'precios'=>$bss,
            'estados'=>array(
            'estado_compra'=>2,
            'estado_venta'=>2)
        )
    );

    return $app->json($a);

});