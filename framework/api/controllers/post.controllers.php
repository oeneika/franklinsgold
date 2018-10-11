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
/**
    * Inicio de sesión
    *
    * @return json
*/  
$app->post('/login', function() use($app) {
    $u = new Model\Users;   

    return $app->json($u->login());   
});

/**
    * Registro de un usuario
    *
    * @return json
*/
$app->post('/register', function() use($app) {
    $u = new Model\Users; 

    return $app->json($u->register());   
});

/**
    * Recuperar contraseña perdida
    *
    * @return json
*/
$app->post('/lostpass', function() use($app) {
    $u = new Model\Users; 

    return $app->json($u->lostpass());   
});


/**
  * Acción vía ajax de usuarios en api/usuarios/crear
  *
  * @return json
*/
$app->post('/usuarios/crear', function() use($app) {
    $u = new Model\Users; 
  
    return $app->json($u->add());   
  });
  
  
/**
    * Acción vía ajax de usuarios en api/usuarios/editar
    *
    * @return json
*/
$app->post('/usuarios/editar', function() use($app) {
    $u = new Model\Users; 

    return $app->json($u->edit());   
});

/**
    * Acción vía ajax de usuarios en api/usuarios/subirdocumentos
    *
    * @return json
*/
$app->post('/usuarios/subirdocumentos', function() use($app) {
    $u = new Model\Users; 

    return $app->json($u->UploadDocuments());   
});



/**
 * Endpoint crear para sucursales
 *
 * @return json
*/
$app->post('/sucursal/crear', function() use($app) {
    $s = new Model\Sucursales; 
    
    return $app->json($s->add());   
});

/**
 * Endpoint editar para sucursales
 *
 * @return json
*/
$app->post('/sucursal/editar', function() use($app) {
    $s = new Model\Sucursales; 
    
    return $app->json($s->edit());   
});

/**
 * Endpoint crear para sucursales
 *
 * @return json
*/
$app->post('/sucursal/get', function() use($app) {
    $s = new Model\Sucursales; 
    
    return $app->json($s->get());   
});

 /**
 * Endpoints para moneda
 *
 * @return json
*/
$app->post('/monedas/crear', function() use($app) {
    $m = new Model\Monedas; 
  
    return $app->json($m->add());   
  });
  
  $app->post('/monedas/editar', function() use($app) {
    $m = new Model\Monedas; 
  
    return $app->json($m->edit());   
  });

  /**
 * Endpoints para origen
 *
 * @return json
*/
$app->post('/origen/crear', function() use($app) {
    $o = new Model\Origen; 

    return $app->json($o->add());   
});

$app->post('/origen/editar', function() use($app) {
  $o = new Model\Origen; 

  return $app->json($o->edit());   
});

/**
 * Endpoints para transacciones
 *
 * @return json
*/
$app->post('/transaccion/crear', function() use($app) {
  $t = new Model\Transaccion; 

  return $app->json($t->add());   
});



/**
 * Endpoint para iniciar una transaccion 
 *
 * @return json
*/
$app->post('/transaccion/iniciar/qr', function() use($app) {
    $t = new Model\Transaccion; 
  
    return $app->json($t->receptorQr());   
});

$app->post('/transaccion/qr/concretar', function() use($app) {
    $t = new Model\Transaccion; 
  
    return $app->json($t->tokenConfirmation());   
});



$app->post('/transaccion/intercambioAfiliado', function() use($app) {
    $t = new Model\Transaccion; 
  
    return $app->json($t->intercambiosAfiliados());   
});

/**
 * Endpoints para afiliados
 *
 * @return json
*/
$app->post('/afiliados/crear', function() use($app) {
    $a = new Model\Afiliados; 

    return $app->json($a->add());   
});


$app->post('/afiliados/editar', function() use($app) {
    $a = new Model\Afiliados; 

    return $app->json($a->edit());   
});

/**
 * Endpoint para comprayventa
 *
 * @return json
*/
$app->post('/orden/crear', function() use($app) {
    $o = new Model\Orden; 

    return $app->json($o->createOrden());   
});

 /**
 * Servicio que devuelve gramos de oro/plata comprados
 *
 * @return json
*/
$app->post('/orden/getGramos', function() use($app) {
    $o = new Model\Orden; 

    return $app->json($o->getGramosOroPlata());      
});

 /**
 * Servicio que devuelve las ultimas cinco ordenes concretadas
 *
 * Recibe el tipo de orden, el tipo de gramo y el email del cliente
 * @return json
*/
$app->post('/orden/getUltTransacciones', function() use($app) {
    $o = new Model\Orden; 

    return $app->json($o->getUltTransacciones());      
});

/**
 * Servicio que devuelve todas las transacciones de un usuario
 *
 * Recibe el email del cliente
 * @return json
*/
$app->post('/orden/getOrdenesByUser', function() use($app) {
    $o = new Model\Orden; 

    return $app->json($o->getOrdenesByUser());      
});

 /**
 * Crea una orden en espera por un intercambio en un comercio afiliado
 *
 * @return json
*/
$app->post('/orden/intercambiocomercio', function() use($app) {
    $o = new Model\Orden; 

    return $app->json($o->createOrdenEnEspera());      
});

/**
 * Concreta una orden en espera por un intercambio en un comercio afiliado
 *
 * @return json
*/
$app->post('/orden/concretarintercambiocomercio', function() use($app) {
    $o = new Model\Orden; 

    return $app->json($o->concreteOrdenEnEspera());      
});

/**
 * Endpoints para divisas
 *
 * @return json
*/
$app->post('/divisa/crear', function() use($app) {
    $d = new Model\Divisa; 

    return $app->json($d->add());   
});


$app->post('/divisa/editar', function() use($app) {
    $d = new Model\Divisa; 

    return $app->json($d->edit());   
});

/**
 * Endpoint para landing
 *
 * @return json
*/
$app->post('/landing', function() use($app) {
    $l = new Model\Landing; 

    return $app->json($l->foo());   
});

/**
 * Endpoints para rangos
 *
 * @return json
*/
$app->post('/rango/crear', function() use($app) {
    $r = new Model\Rango; 

    return $app->json($r->add());   
});


$app->post('/rango/editar', function() use($app) {
    $r = new Model\Rango; 

    return $app->json($r->edit());   
});


/**
 * Endpoint para inicio
 *
 * @return json
*/
$app->post('/inicio', function() use($app) {
    $i = new Model\Inicio; 

    return $app->json($i->foo());   
});


/**
 * Cambia el estado de una notificación
 *
 * @return json
*/
$app->post('/set/notifications', function() use($app) {
    $u = new Model\Users; 

    return $app->json($u->setNotifications());   
});

/**
 * Envía cantidades de gramos poseidas po un comercio afiliado
 *
 * @return json
*/
$app->post('/sendGramos', function() use($app) {
    $o = new Model\Orden; 

    return $app->json($o->sendGramos());   
});