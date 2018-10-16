<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Controllers;

use app\models as Model;
use Ocrend\Kernel\Helpers as Helper;
use Ocrend\Kernel\Router\IRouter;

/**
 * Clase para conectar todos los controladores del sistema y compartir la configuración.
 * Inicializa aspectos importantes de una página, como el sistema de plantillas twig.
 *
 * @author Brayan Narváez <prinick@ocrend.com>
 */

abstract class Controllers {
    
    /**
      * Obtiene el objeto del template 
      *
      * @var \Twig_Environment
    */
    protected $template;

    /**
      * Verifica si está definida la ruta /id como un integer >= 1
      *
      * @var bool
    */
    protected $isset_id = false;

    /**
      * Tiene el valor de la ruta /método
      *
      * @var string|null
    */
    protected $method;
    
    /**
      * Arreglo con la información del usuario conectado actualmente.
      *
      * @var array 
    */
    protected $user = array();

    /**
      * Contiene información sobre el estado del usuario, si está o no conectado.
      *
      * @var bool
    */
    private $is_logged = false;

    /** 
      * Parámetros de configuración para el controlador con la forma:
      * 'parmáetro' => (bool) valor
      *
      * @var array
    */
    private $controllerConfig;

    /**
      * Configuración inicial de cualquier controlador
      *
      * @param IRouter $router: Instancia de un Router
      * @param array $configController: Arreglo de configuración con la forma  
      *     'users_logged' => bool, # Configura el controlador para solo ser visto por usuarios logeados
      *     'users_not_logged' => bool, # Configura el controlador para solo ser visto por !(usuarios logeados)
      *
    */
    protected function __construct(IRouter $router, $configController = []) {
        global $config, $http, $session, $cookie;

        # Verificar si está logeado el usuario
        $this->is_logged = null != $session->get($cookie->get('session_hash') . '__user_id');

        # Establecer la configuración para el controlador
        $this->setControllerConfig($configController);

        # Twig Engine http://gitnacho.github.io/Twig/
        $this->template = new \Twig_Environment(new \Twig_Loader_Filesystem('./app/templates/'), array(
            # ruta donde se guardan los archivos compilados
            'cache' => $config['twig']['compiled_dir'],
            # false para caché estricto, cero actualizaciones, recomendado para páginas 100% estáticas
            'auto_reload' => !$config['twig']['cache'],
            # en true, las plantillas generadas tienen un método __toString() para mostrar los nodos generados
            'debug' => !$config['build']['production'],
            # el charset utilizado por los templates
            'charset' => $config['twig']['charset'],
            # true para evitar ignorar las variables no definidas en el template
            'strict_variables' => $config['twig']['strict_variables'],
            # false para evitar el auto escape de html por defecto (no recomendado)
            'autoescape' => $config['twig']['autoescape']
        )); 
        
        # Request global
        $this->template->addGlobal('get', $http->query->all());
        $this->template->addGlobal('server', $http->server->all());
        $this->template->addGlobal('session', $session->all());
        $this->template->addGlobal('cookie', $cookie->all());
        $this->template->addGlobal('config', $config);
        $this->template->addGlobal('is_logged', $this->is_logged);

        

        # Datos del usuario actual y de las divisas para el header
        if ($this->is_logged) {

          $d = new Model\Divisa();
          $u = new Model\Users();

          $this->user = $u->getOwnerUser();
          $notifiaciones = $u->getNotifications($this->user["id_user"]);
          $oro = ($d->getDivisas("precio_dolares,precio_dolares_venta","nombre_divisa='Oro Franklin'"))[0];
          $plata = ($d->getDivisas("precio_dolares,precio_dolares_venta","nombre_divisa='Plata Franklin'"))[0];

          $this->template->addGlobal('owner_user', $this->user);
          $this->template->addGlobal('notifications', $notifiaciones);
          $this->template->addGlobal('precio_oro_global', $oro);
          $this->template->addGlobal('precio_plata_global', $plata);
        }

        # Extensiones
        $this->template->addExtension(new Helper\Functions);

        # Debug disponible en twig
        if(!$config['build']['production']) {
          $this->template->addExtension(new \Twig_Extension_Debug());
        }

        # Verificar para quién está permitido este controlador
        $this->knowVisitorPermissions();

        # Auxiliares
        $this->method = $router->getMethod();
        $this->isset_id = $router->getID(true);
    }

    /**
     * Establece los parámetros de configuración de un controlador
     *
     * @param IRouter $router: Instancia de un Router
     * @param array|null $config: Arreglo de configuración   
     *
     * @return void
     */
    private function setControllerConfig($config) {
      $this->controllerConfig = array_merge(array(
        'users_logged' => false,
        'users_not_logged' => false,
        'users_admin'=> false,
        'users_vendedor'=> false,
        'users_cliente'=> false,
        'users_supervisoradmin'=> false,
        'users_vendedorsupervisoradmin'=> false,
      ), $config);
    }
    
    /**
     * Acción que regula quién entra o no al controlador según la configuración
     *
     * @return void
     */
    private function knowVisitorPermissions() {
      global $config;

      # Sólamente usuarios logeados
      if ($this->controllerConfig['users_logged'] && !$this->is_logged) {
        Helper\Functions::redir($config['build']['url'] . 'welcome');
      }

      # Sólamente usuarios tipo admin
      if ($this->controllerConfig['users_admin'] && !$this->user['tipo']==0) {
        Helper\Functions::redir($config['build']['url'] . 'home');
      }

      # Sólamente usuarios tipo cliente
      if ($this->controllerConfig['users_cliente'] && !$this->user['tipo']==2 ) {
        Helper\Functions::redir($config['build']['url'] . 'home');
      }

      # Sólamente usuarios tipo vendedor(sólo de sucursal), supervisor(sólo de sucursal) y admin
      if ($this->controllerConfig['users_vendedorsupervisoradmin'] && ($this->user['tipo']==2 or !$this->user['id_comercio_afiliado']==null  ) ) {
        Helper\Functions::redir($config['build']['url'] . 'home');
      }

      # Sólamente usuarios tipo vendedor y admin
      if ($this->controllerConfig['users_supervisoradmin'] && ($this->user['tipo']==1 or $this->user['tipo']==2 or !$this->user['id_comercio_afiliado']==null) ){
        Helper\Functions::redir($config['build']['url'] . 'home');
      }

      # Sólamente usuarios no logeados
      if ($this->controllerConfig['users_not_logged'] && $this->is_logged) {
        Helper\Functions::redir();
      }
    }

}