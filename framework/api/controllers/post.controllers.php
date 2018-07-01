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
  $u = new Model\Usuarios; 

  return $app->json($u->crear());   
});


/**
  * Acción vía ajax de usuarios en api/usuarios/editar
  *
  * @return json
*/
$app->post('/usuarios/editar', function() use($app) {
  $u = new Model\Usuarios; 

  return $app->json($u->editar());   
});

/**
  * Acción vía ajax de usuarios en api/sucursal/crear
  *
  * @return json
*/
$app->post('/sucursal/crear', function() use($app) {
  $s = new Model\Sucursal; 

  return $app->json($s->add());   
});