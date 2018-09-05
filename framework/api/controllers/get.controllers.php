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
 * Servicio que devuelve el precio del BsS almacenado en la base de datos
 *
 * @return json
*/
$app->get('/get/precioBsS', function() use($app) {
    $d = new Model\Divisa; 

    return $app->json($d->getDivisas("precio_dolares","nombre_divisa=Bolívar Soberano"));      
});