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
  * Acción vía ajax de monedas en api/monedas/crear
  *
  * @return json
*/
$app->post('/monedas/crear', function() use($app) {
    $m = new Model\Monedas; 
  
    return $app->json($m->add());   
  });
  
  
  /**
    * Acción vía ajax de monedas en api/monedas/editar
    *
    * @return json
  */
  $app->post('/monedas/editar', function() use($app) {
    $m = new Model\Monedas; 
  
    return $app->json($m->edit());   
  });

  /**
 * Endpoint para origen
 *
 * @return json
*/
$app->post('/origen/crear', function() use($app) {
    $o = new Model\Origen; 

    return $app->json($o->add());   
});

/**
 * Endpoint para origen
 *
 * @return json
*/
$app->post('/origen/editar', function() use($app) {
  $o = new Model\Origen; 

  return $app->json($o->edit());   
});

/**
 * Endpoint para origen
 *
 * @return json
*/
$app->post('/transaccion/crear', function() use($app) {
  $t = new Model\Transaccion; 

  return $app->json($t->add());   
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