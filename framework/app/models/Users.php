<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\models;

use app\models as Model;
use Ocrend\Kernel\Helpers as Helper;
use Ocrend\Kernel\Models\Models;
use Ocrend\Kernel\Models\IModels;
use Ocrend\Kernel\Models\ModelsException;
use Ocrend\Kernel\Models\Traits\DBModel;
use Ocrend\Kernel\Router\IRouter;
use Ocrend\Kernel\Helpers\Functions;

/**
 * Modelo Users
 */
class Users extends Models implements IModels {
    use DBModel;

    /**
     * Máximos intentos de inincio de sesión de un usuario
     *
     * @var int
     */
    const MAX_ATTEMPTS = 5;

    /**
     * Tiempo entre máximos intentos en segundos
     *
     * @var int
     */
    const MAX_ATTEMPTS_TIME = 120; # (dos minutos)

    /**
     * Log de intentos recientes con la forma 'email' => (int) intentos
     *
     * @var array
     */
    private $recentAttempts = array();


    /**
     * Datos del usuario usados por el crud Usuarios
     */
    private $primer_nombre;
    private $segundo_nombre;
    private $primer_apellido;
    private $segundo_apellido;
    private $tipo;
    private $usuario;
    private $pass;
    private $pass2;
    private $sexo;
    private $telefono;
    private $email;


    /**
     * Hace un set() a la sesión login_user_recentAttempts con el valor actualizado.
     *
     * @return void
    */
    private function updateSessionAttempts() {
        global $session;

        $session->set('login_user_recentAttempts', $this->recentAttempts);
    }

    /**
     * Revisa si las contraseñas son iguales
     *
     * @param string $pass : Contraseña sin encriptar
     * @param string $pass_repeat : Contraseña repetida sin encriptar
     *
     * @throws ModelsException cuando las contraseñas no coinciden
     */
    private function checkPassMatch(string $pass, string $pass_repeat) {
        if ($pass != $pass_repeat) {
            throw new ModelsException('Las contraseñas no coinciden.');
        }
    }

    /**
     * Verifica el email introducido, tanto el formato como su existencia en el sistema
     *
     * @param string $email: Email del usuario
     *
     * @throws ModelsException en caso de que no tenga formato válido o ya exista
     */
    private function checkEmail(string $email) {
        # Formato de email
        if (!Helper\Strings::is_email($email)) {
            throw new ModelsException('El email no tiene un formato válido.');
        }
        # Existencia de email
        $email = $this->db->scape($email);
        $query = $this->db->select('id_user', 'users', null, "email='$email'", 1);
        if (false !== $query) {
            throw new ModelsException('El email introducido ya existe.');
        }
    }

    /**
     * Restaura los intentos de un usuario al iniciar sesión
     *
     * @param string $email: Email del usuario a restaurar
     *
     * @throws ModelsException cuando hay un error de lógica utilizando este método
     * @return void
     */
    private function restoreAttempts(string $email) {       
        if (array_key_exists($email, $this->recentAttempts)) {
            $this->recentAttempts[$email]['attempts'] = 0;
            $this->recentAttempts[$email]['time'] = null;
            $this->updateSessionAttempts();
        } else {
            throw new ModelsException('Error lógico');
        }
    }

    /**
     * Genera la sesión con el id del usuario que ha iniciado
     *
     * @param array $user_data: Arreglo con información de la base de datos, del usuario
     *
     * @return void
     */
    private function generateSession(array $user_data) {
        global $session, $cookie, $config;
        
        # Generar un session hash
        $cookie->set('session_hash', md5(time()), $config['sessions']['user_cookie']['lifetime']);
        
        # Generar la sesión del usuario
        $session->set($cookie->get('session_hash') . '__user_id',(int) $user_data['id_user']);

        # Generar data encriptada para prolongar la sesión
        if($config['sessions']['user_cookie']['enable']) {
            # Generar id encriptado
            $encrypt = Helper\Strings::ocrend_encode($user_data['id_user'], $config['sessions']['user_cookie']['key_encrypt']);

            # Generar cookies para prolongar la vida de la sesión
            $cookie->set('appsalt', Helper\Strings::hash($encrypt), $config['sessions']['user_cookie']['lifetime']);
            $cookie->set('appencrypt', $encrypt, $config['sessions']['user_cookie']['lifetime']);
        }
    }

    /**
     * Verifica en la base de datos, el email y contraseña ingresados por el usuario
     *
     * @param string $email: Email del usuario que intenta el login
     * @param string $pass: Contraseña sin encriptar del usuario que intenta el login
     *
     * @return bool true: Cuando el inicio de sesión es correcto 
     *              false: Cuando el inicio de sesión no es correcto
     */
    private function authentication(string $email,string $pass) : bool {
        $email = $this->db->scape($email);
        $query = $this->db->select('id_user,pass','users',null, "email='$email'",1);
        
        # Incio de sesión con éxito
        if(false !== $query && Helper\Strings::chash($query[0]['pass'],$pass)) {

            # Restaurar intentos
            $this->restoreAttempts($email);

            # Generar la sesión
            $this->generateSession($query[0]);
            return true;
        }

        return false;
    }

    /**
     * Establece los intentos recientes desde la variable de sesión acumulativa
     *
     * @return void
     */
    private function setDefaultAttempts() {
        global $session;

        if (null != $session->get('login_user_recentAttempts')) {
            $this->recentAttempts = $session->get('login_user_recentAttempts');
        }
    }
    
    /**
     * Establece el intento del usuario actual o incrementa su cantidad si ya existe
     *
     * @param string $email: Email del usuario
     *
     * @return void
     */
    private function setNewAttempt(string $email) {
        if (!array_key_exists($email, $this->recentAttempts)) {
            $this->recentAttempts[$email] = array(
                'attempts' => 0, # Intentos
                'time' => null # Tiempo 
            );
        } 

        $this->recentAttempts[$email]['attempts']++;
        $this->updateSessionAttempts();
    }

    /**
     * Controla la cantidad de intentos permitidos máximos por usuario, si llega al límite,
     * el usuario podrá seguir intentando en self::MAX_ATTEMPTS_TIME segundos.
     *
     * @param string $email: Email del usuario
     *
     * @throws ModelsException cuando ya ha excedido self::MAX_ATTEMPTS
     * @return void
     */
    private function maximumAttempts(string $email) {
        if ($this->recentAttempts[$email]['attempts'] >= self::MAX_ATTEMPTS) {
            
            # Colocar timestamp para recuperar más adelante la posibilidad de acceso
            if (null == $this->recentAttempts[$email]['time']) {
                $this->recentAttempts[$email]['time'] = time() + self::MAX_ATTEMPTS_TIME;
            }
            
            if (time() < $this->recentAttempts[$email]['time']) {
                # Setear sesión
                $this->updateSessionAttempts();
                # Lanzar excepción
                throw new ModelsException('Ya ha superado el límite de intentos para iniciar sesión.');
            } else {
                $this->restoreAttempts($email);
            }
        }
    }      

    /**
     * Obtiene datos del usuario conectado actualmente
     *
     * @param string $select : Por defecto es *, se usa para obtener sólo los parámetros necesarios
     *
     * @throws ModelsException si el usuario no está logeado
     * @return array con datos del usuario conectado
     */
    public function getOwnerUser(string $select = '*') : array {
        if(null !== $this->id_user) {    
               
            $user = $this->db->select($select,'users',null, "id_user='$this->id_user'",1);

            # Si se borra al usuario desde la base de datos y sigue con la sesión activa
            if(false === $user) {
                $this->logout();
            }

            return $user[0];
        } 
           
        throw new \RuntimeException('El usuario no está logeado.');
    }

     /**
     * Realiza la acción de login dentro del sistema
     *
     * @return array : Con información de éxito/falla al inicio de sesión.
     */
    public function login() : array {
        try {
            global $http;

            # Definir de nuevo el control de intentos
            $this->setDefaultAttempts();   

            # Obtener los datos $_POST
            $email = strtolower($http->request->get('email'));
            $pass = $http->request->get('pass');

            # Verificar que no están vacíos
            if (Helper\Functions::e($email, $pass)) {
                throw new ModelsException('Credenciales incompletas.');
            }
            
            # Añadir intentos
            $this->setNewAttempt($email);
        
            # Verificar intentos 
            $this->maximumAttempts($email);

            # Autentificar
            if ($this->authentication($email, $pass)) {
                # trae la info del usuario para su uso en app movil
                $select = 'id_user,usuario, email, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, sexo, tipo, telefono';
                $query = $this->db->select($select, 'users',null,"email = '$email'");

                return array('success' => 1, 'message' => 'Conectado con éxito.','data'=>$query);
            }
            
            throw new ModelsException('Credenciales incorrectas.');

        } catch (ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }        
    }

    /**
     * Realiza la acción de registro dentro del sistema
     *
     * @return array : Con información de éxito/falla al registrar el usuario nuevo.
     */
    public function register() : array {
        try {
            global $http;



            # Obtener los datos $_POST
            $user_data = $http->request->all();

            #Verificar que se este accediendo desde la web
            $tipo = array_key_exists('tipo',$user_data)?0:2;

            if ($tipo !== 0 && $tipo !== 1 && $tipo !== 2){
                throw new ModelsException('Tipo de usuario invalido');
            }

            # Verificar que no están vacíos
            if (!array_key_exists('primer_nombre',$user_data) || Functions::emp($user_data['primer_nombre'])) {
                throw new ModelsException('El primer nombre no debe estar vacio');
            }

            if (!array_key_exists('primer_apellido',$user_data) || Functions::emp($user_data['primer_apellido'])) {
                throw new ModelsException('El primer apellido no debe estar vacio');
            }

            if (!array_key_exists('usuario',$user_data) || Functions::emp($user_data['usuario'])) {
                throw new ModelsException('El usuario no debe estar vacio');
            }

            $username = $this->db->scape($user_data['usuario']);
            $query = $this->db->select('usuario','users',null,"usuario = '$username'");
            if (false !== $query){
                throw new ModelsException('Ya existe un usuario con ese nombre');
            }

            if (!array_key_exists('email',$user_data) || Functions::emp($user_data['email'])) {
                throw new ModelsException('El email no debe estar vacio');
            }

            if (!array_key_exists('pass',$user_data)  || Functions::emp($user_data['pass']) ) {
                throw new ModelsException('El password no debe estar vacio');
            }

            if (!array_key_exists('pass_repeat',$user_data) || Functions::emp($user_data['pass_repeat'])) {
                throw new ModelsException('Por favor repita el password');
            }

            if(!array_key_exists('segundo_nombre',$user_data)){
                throw new ModelsException('Campo segundo nombre no definido');
            }

            if(!array_key_exists('segundo_apellido',$user_data)){
                throw new ModelsException('Campo segundo apellido no definido');
            }

            if(!array_key_exists('telefono',$user_data)){
                throw new ModelsException('Campo telefono no definido');
            }

            if (strlen($user_data['telefono']) < 11){
                throw new ModelsException("Telefono invalido, debe tener al menos 11 digitos");              
            }

            if(!array_key_exists('sexo',$user_data)){
                throw new ModelsException('Campo sexo no definido');
            }

            # Verificar email 
            $this->checkEmail($user_data['email']);

            # Veriricar contraseñas
            $this->checkPassMatch($user_data['pass'], $user_data['pass_repeat']);

            # Registrar al usuario
            $id_user = $this->db->insert('users', array(
                'primer_nombre' => $user_data['primer_nombre'],
                'segundo_nombre' => $user_data['segundo_nombre'],
                'primer_apellido' => $user_data['primer_apellido'],
                'segundo_apellido' => $user_data['segundo_apellido'],
                'usuario' => $user_data['usuario'],
                'sexo' => $user_data['sexo'],
                'email' => $user_data['email'],
                'telefono' => $user_data['telefono'],
                'tipo' => $tipo,
                'pass' => Helper\Strings::hash($user_data['pass'])
            ));

                #Concatena una palabra para evitar repeticiones del codigoqr
                $conc = "usuarios".$id_user;

                # Url del codigo qr
                $url = "https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=$conc";

                # Ruta en la que se guardara la imagen
                $img = "../views/img/codigos/usuarios/$conc.png";
                file_put_contents($img, file_get_contents($url));

                #Se actualiza la db con la ruta de la imagen
                $this->db->update('users',array(
                    'codigo_qr'=> $img
                ), "id_user = '$id_user'");


            # Iniciar sesión
            $this->generateSession(array(
                'id_user' => $id_user
            ));

            return array('success' => 1, 'message' => 'Registrado con éxito.');
        } catch (ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }        
    }
    
    /**
      * Envía un correo electrónico al usuario que quiere recuperar la contraseña, con un token y una nueva contraseña.
      * Si el usuario no visita el enlace, el sistema no cambiará la contraseña.
      *
      * @return array<string,integer|string>
    */  
    public function lostpass() : array {
        try {
            global $http, $config;

            # Obtener datos $_POST
            $email = $http->request->get('email');
            
            # Campo lleno
            if (Helper\Functions::emp($email)) {
                throw new ModelsException('El campo email debe estar lleno.');
            }

            # Filtro
            $email = $this->db->scape($email);

            # Obtener información del usuario 
            $user_data = $this->db->select('id_user,usuario', 'users', null, "email='$email'", 1);

            # Verificar correo en base de datos 
            if (false === $user_data) {
                throw new ModelsException('El email no está registrado en el sistema.');
            }

            # Generar token y contraseña 
            $token = md5(time());
            $pass = uniqid();
            $link = $config['build']['url'] . 'lostpass?token='.$token.'&user='.$user_data[0]['id_user'];

            # Construir mensaje y enviar mensaje
            $HTML = 'Hola <b>'. $user_data[0]['usuario'] .'</b>, ha solicitado recuperar su contraseña perdida, si no ha realizado esta acción no necesita hacer nada.
					<br />
					<br />
					Para cambiar su contraseña por <b>'. $pass .'</b> haga <a href="'. $link .'" target="_blank">clic aquí</a> o en el botón de recuperar.';

            # Enviar el correo electrónico
            $dest = array();
			$dest[$email] = $user_data[0]['usuario'];
            $email_send = Helper\Emails::send($dest,array(
                # Título del mensaje
                '{{title}}' => 'Recuperar contraseña de ' . $config['build']['name'],
                # Url de logo
                '{{url_logo}}' => $config['build']['url'],
                # Logo
                '{{logo}}' => $config['mailer']['logo'],
                # Contenido del mensaje
                '{{content}} ' => $HTML,
                # Url del botón
                '{{btn-href}}' => $link,
                # Texto del boton
                '{{btn-name}}' => 'Recuperar Contraseña',
                # Copyright
                '{{copyright}}' => '&copy; '.date('Y') .' <a href="'.$config['build']['url'].'">'.$config['build']['name'].'</a> - Todos los derechos reservados.'
              ),0);

            # Verificar si hubo algún problema con el envío del correo
            if(false === $email_send) {
                throw new ModelsException('No se ha podido enviar el correo electrónico.');
            }

            # Actualizar datos 
            $id_user = $user_data[0]['id_user'];
            $this->db->update('users',array(
                'tmp_pass' => Helper\Strings::hash($pass),
                'token' => $token
            ),"id_user='$id_user'",1);

            return array('success' => 1, 'message' => 'Se ha enviado un enlace a su correo electrónico.');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }

    /**
     * Desconecta a un usuario si éste está conectado, y lo devuelve al inicio
     *
     * @return void
     */    
    public function logout() {
        global $session, $cookie;
	    
        $session->remove($cookie->get('session_hash') . '__user_id');
        foreach($cookie->all() as $name => $value) {
            $cookie->remove($name);
        }

        Helper\Functions::redir();
    }

    /**
     * Cambia la contraseña de un usuario en el sistema, luego de que éste haya solicitado cambiarla.
     * Luego retorna al sitio de inicio con la variable GET success=(bool)
     *
     * La URL debe tener la forma URL/lostpass?token=TOKEN&user=ID
     *
     * @return void
     */  
    public function changeTemporalPass() {
        global $config, $http;
        
        # Obtener los datos $_GET 
        $id_user = $http->query->get('user');
        $token = $http->query->get('token');

        $success = false;
        if (!Helper\Functions::emp($token) && is_numeric($id_user) && $id_user >= 1) {
            # Filtros a los datos
            $id_user = $this->db->scape($id_user);
            $token = $this->db->scape($token);
            # Ejecutar el cambio
            $response = $this->db->query("UPDATE users SET pass=tmp_pass, tmp_pass=NULL, token=NULL
            WHERE id_user='$id_user' AND token='$token' LIMIT 1;");
            # Éxito
            $success = true;
        }
        
        # Devolover al sitio de inicio
        Helper\Functions::redir($config['build']['url'] . 'login?sucess=' . (int) $success);
    }


    #A partir de aquí estan los metodos utilziados por el crud Usuarios

    /**
     * Revisa errores en el formulario
     * 
     * @return exception
    */ 
    private function errors(bool $edit = false){

        global $http;

        $this->primer_nombre = $http->request->get('primer_nombre');
        $this->segundo_nombre = $http->request->get('segundo_nombre');
        $this->primer_apellido = $http->request->get('primer_apellido');
        $this->segundo_apellido = $http->request->get('segundo_apellido');
        
        $this->tipo = $http->request->get('tipo');
        $this->usuario = $http->request->get('usuario');
        $this->pass = $http->request->get('pass');
        $this->pass2 = $http->request->get('pass2');
        $this->sexo = $http->request->get('sexo');
        $this->telefono = $http->request->get('telefono');
        $this->email = $http->request->get('email');


        # Verificar que no están vacíos
        if (Helper\Functions::e($this->primer_nombre, $this->primer_apellido,$this->telefono)) {
            throw new ModelsException('Todos los campos marcados con "*" son necesarios.');
        }

        if (Helper\Functions::e($this->pass,$this->pass2,$this->usuario,$this->email) and !$edit) {
            throw new ModelsException('Todos los campos marcados con "*" son necesarios.');
        }

        if($this->tipo!=0 and $this->tipo!=1 and $this->tipo!=2) {
            throw new ModelsException('Tipo de usuario no válido.');
        }


        if($this->sexo!="m" and $this->sexo!="f") {
            throw new ModelsException('Sexo no válido.');
        }

        if (strlen($this->telefono) < 11){
            throw new ModelsException("Telefono invalido, debe tener al menos 11 digitos");              
        }


        # Veriricar contraseñas y email
        if (!$edit) {           
             $this->checkPassMatch($this->pass, $this->pass2);
             $this->checkEmail($this->email);

            #Revisa la exitencia del nombre de usuario que se está introduciendo
            if($this->checkUsuario($this->usuario) != null){
                throw new ModelsException('El nombre de usuario ya esta en uso.'); 
            }
        }

    }

    /**
     * Agrega usuarios 
     * 
     * @return array
    */ 
    public function add() : array {
        try {

            #Revisa errores del formulario
            $this->errors();

            
            $u = array(
            'primer_nombre' => $this->primer_nombre,
            'segundo_nombre' => $this->segundo_nombre,
            'primer_apellido' => $this->primer_apellido,
            'segundo_apellido' => $this->segundo_apellido,
            'usuario' => $this->usuario,
            'pass' => Helper\Strings::hash($this->pass),
            'sexo' => $this->sexo,
            'telefono' => $this->telefono,
            'email' => $this->email
            );
  
  
             #Array con datos validos para el insert
             $data = array();
  
             #Valida que los datos no esten vacios y los inserta en el array "data"
             foreach ($u as $key=>$val) {
               if(NULL !== $u[$key] && !Functions::emp($u[$key])){
                 $data[$key] = $u[$key];
               }
             }
        
            $data['tipo'] = $this->tipo;
  
            # Registrar al usuario
            $id_user =  $this->db->insert('users',$data);

            #Concatena una palabra para evitar repeticiones del codigoqr
            $conc = "usuarios".$id_user;

            # Url del codigo qr
            $url = "https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=$conc";

            # Ruta en la que se guardara la imagen
            $img = "../views/img/codigos/usuarios/$conc.png";
            file_put_contents($img, file_get_contents($url));

            #Se actualiza la db con la ruta de la imagen
            $this->db->update('users',array(
                'codigo_qr'=> $img
            ), "id_user = '$id_user'");

            return array('success' => 1, 'message' => 'Usuario creado con éxito!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }


    /**
     * Edita usuarios 
     * 
     * @return array
    */ 
    public function edit() : array {
        try {

            global $http;

            $id = $http->request->get('id_user');

            $this->errors(true);

            /*if($this->tipo == 0 ){
                $this->tipo = 2;
            }*/

            $u = array(
                'primer_nombre' => $this->primer_nombre,
                'segundo_nombre' => $this->segundo_nombre,
                'primer_apellido' => $this->primer_apellido,
                'segundo_apellido' => $this->segundo_apellido,
                'tipo' => $this->tipo,
                'sexo' => $this->sexo,
                'telefono' => $this->telefono
            );
            
            
             #Si la password no esta vacía le hago el hash y la introduzco al array
            if( ! (Helper\Functions::e($this->pass)) ){
                $u['pass'] = Helper\Strings::hash($this->pass);
                            
            }
            

            #Array con datos validos para el update
            $data = array();

            #Valida que los datos no esten vacios y los inserta en el array "data"
            foreach ($u as $key=>$val) {
                if(NULL !== $u[$key] && !Functions::emp($u[$key])){
                    $data[$key] = $u[$key];
                }
            }

            $data['tipo'] = $this->tipo;
            
            #Edita un usuario
            $this->db->update('users',$data,"id_user = '$id'",'1');

            return array('success' => 1, 'message' => 'Usuario editado con éxito!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }


    /**
     * Verifica la existencia del nombre de usuario
     * 
     * @return array
    */ 
    public function checkUsuario(String $nombre_usuario) {
        
        return $this->db->select("usuario",'users',null,"usuario='$nombre_usuario'");

    }

    /**
     * Verifica la existencia del nombre de usuario
     * 
     * @return array
    */ 
    public function checkTelefono(int $numero_telefonico) {
        
        return $this->db->select("usuario",'users',null,"usuario=$numero_telefonico");

    }

    /**
     * Eliminar usuario
    */
    final public function del() {
        Global $config;

       $res = $this->db->delete('users',"id_user='$this->id'",'1');

      # Redireccionar al controlador usuarios con un success=true
      Functions::redir($config['build']['url'] . 'usuarios/&success=true');

    }



    /**
     * Obtiene datos de un usuario según su id en la base de datos
     *    
     * @param int $id: Id del usuario a obtener
     * @param string $select : Por defecto es *, se usa para obtener sólo los parámetros necesarios 
     *
     * @return false|array con información del usuario
     */   
    public function getUserById(int $id, string $select = '*') {
        return $this->db->select($select,'users',null,"id_user='$id'",1);
    }
    
    /**
     * Obtiene a todos los usuarios
     *    
     * @param string $select : Por defecto es *, se usa para obtener sólo los parámetros necesarios 
     *
     * @return false|array con información de los usuarios
     */  
    public function getUsers(string $select = '*',string $where = "1=1") {
        return $this->db->select($select,'users',null,$where);
    }



    /**
     * __construct()
     */
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
		$this->startDBConexion();
    }
}