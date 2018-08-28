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
    private $id_sucursal;
    private $id_comercio;
    private $numero_cuenta;
    private $tipo_cliente;


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
            global $http,$config;

            # Obtener los datos $_POST
            $user_data = $http->request->all();

            # Obtener los datos $_FILES los cuales contienen las imagenes cargadas
            $fotos = $_FILES;
            //var_dump($fotos);
           
            #Todo usuario que se registre será un cliente
            $tipo = 2;

            if ($tipo !== 0 && $tipo !== 1 && $tipo !== 2){
                throw new ModelsException('Tipo de usuario inválido');
            }

            # Verificar que no están vacíos
            if (!array_key_exists('primer_nombre',$user_data) || Functions::emp($user_data['primer_nombre'])) {
                throw new ModelsException('El primer nombre no debe estar vacio');
            }

            if (!array_key_exists('primer_apellido',$user_data) || Functions::emp($user_data['primer_apellido'])) {
                throw new ModelsException('El primer apellido no debe estar vacio');
            }

            if (!array_key_exists('foto_documento_identidad',$fotos)) {
                throw new ModelsException('Debe subir una foto de un documento de identidad.');
            }

            if ($fotos["foto_documento_identidad"]["error"] != 0) {
                throw new ModelsException('Hubo un error cargando el documento de identidad, intente de nuevo.');
            }

            if ( !strpos($fotos["foto_documento_identidad"]["type"], "jpeg") and !strpos($fotos["foto_documento_identidad"]["type"], "jpg") and
                !strpos($fotos["foto_documento_identidad"]["type"], "png") ){
                    throw new ModelsException('El documento de identidad debe ser una imagen.');
            }

            if (array_key_exists('foto_pasaporte',$fotos)) {

                if ($fotos["foto_pasaporte"]["error"] != 0) {
                    throw new ModelsException('Hubo un error cargando el pasaporte, intente de nuevo.');
                }

                if ( !strpos($fotos["foto_pasaporte"]["type"], "jpeg") and !strpos($fotos["foto_pasaporte"]["type"], "jpg") and
                !strpos($fotos["foto_pasaporte"]["type"], "png") ){
                    throw new ModelsException('El pasaporte debe ser una imagen.');
                }

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
                throw new ModelsException('El email no debe estar vacío');
            }

            if(!array_key_exists('telefono',$user_data)){
                throw new ModelsException('Campo teléfono no definido');
            }

            if (!ctype_digit($user_data['telefono'])){
                throw new ModelsException("Teléfono inválido, debe ser numérico.");              
            }

            if ( strlen($user_data['telefono']) < 11 ){
                throw new ModelsException("Teléfono inválido, debe tener al menos 11 dígitos");              
            }

            if (!array_key_exists('pass',$user_data)  || Functions::emp($user_data['pass']) ) {
                throw new ModelsException('El password no debe estar vacio');
            }

            if (!array_key_exists('pass_repeat',$user_data) || Functions::emp($user_data['pass_repeat'])) {
                throw new ModelsException('Por favor repita el password');
            }
        
            if(!array_key_exists('numero_cuenta',$user_data)){
                throw new ModelsException('Debe introducir un número de cuenta');
            }
            
            if ( strlen($user_data['numero_cuenta']) != 20  ){
                throw new ModelsException("Número de cuenta inválido.");              
            }

            $nro_cuenta = $this->db->scape($user_data['numero_cuenta']);
            $consulta = $this->db->select("numero_cuenta","users",null,"numero_cuenta='$nro_cuenta'");
            if ( false != $consulta ){
                throw new ModelsException("El número de cuenta ya esta asocioado a otro usuario.");              
            }

            if(!array_key_exists('sexo',$user_data)){
                throw new ModelsException('Campo sexo no definido');
            }

            #Verificar que solo existan letras en los nombres y apellidos
            if(!Helper\Strings::only_letters($user_data['primer_nombre'])){
                throw new ModelsException('El primer nombre solo puede contener letras.');
            }

            if( array_key_exists('segundo_nombre',$user_data) && !Functions::emp($user_data['segundo_nombre'])  ){

                if(!Helper\Strings::only_letters($user_data['segundo_nombre'])){
                    throw new ModelsException('El segundo nombre solo puede contener letras.');
                }

            }

            if(!Helper\Strings::only_letters($user_data['primer_apellido'])){
                throw new ModelsException('El primer apellido solo puede contener letras');
            }

            if( array_key_exists('segundo_apellido',$user_data) && !Functions::emp($user_data['segundo_apellido'])  ){

                if(!Helper\Strings::only_letters($user_data['segundo_apellido'])){
                    throw new ModelsException('El segundo apellido solo puede contener letras.');
                }

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
                'numero_cuenta' => $user_data['numero_cuenta'],
                'tipo' => $tipo,
                'tipo_cliente' => 'Simple',
                'pass' => Helper\Strings::hash($user_data['pass'])
            ));

            /*#Concatena una palabra para evitar repeticiones del codigoqr
            $conc = "usuarios".$id_user;

            # Url del codigo qr
            $url = "https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=$conc";

            # Ruta en la que se guardara la imagen
            $img = "../views/img/codigos/usuarios/$conc.png";
            file_put_contents($img, file_get_contents($url));*/

            $path = "../";

            #Se guarda la foto del documento de identidad
            $dir_documento = "views/img/documentos/usuarios/documentoidentidad".$id_user.".png";
            $tmp_name = $fotos["foto_documento_identidad"]["tmp_name"];
            // basename() puede evitar ataques de denegación de sistema de ficheros;
            // podría ser apropiada más validación/saneamiento del nombre del fichero
            $name = basename($fotos["foto_documento_identidad"]["name"]);
            move_uploaded_file($tmp_name, "$path"."$dir_documento");

                
            #Si se cargó la foto del pasaporte se guarda
            $dir_pasaporte=null;
            if (array_key_exists('foto_pasaporte',$fotos)) {               
                $dir_pasaporte = "views/img/documentos/usuarios/pasaporte".$id_user.".png";
                           
                $tmp_name = $fotos["foto_pasaporte"]["tmp_name"];
                // basename() puede evitar ataques de denegación de sistema de ficheros;
                // podría ser apropiada más validación/saneamiento del nombre del fichero
                $name = basename($fotos["foto_pasaporte"]["name"]);
                move_uploaded_file($tmp_name, "$path"."$dir_pasaporte");
                    
            }

            # Crea la relación con los documentos
            $id_documentos = $this->db->insert('documentos', array(
                'documento_identidad' => $config['build']['url'] . $dir_documento,
                'pasaporte' => $config['build']['url'] . $dir_pasaporte
            ));

            #Se actualiza la db con la ruta de los documentos
            $this->db->update('users',array(
                'id_documentos'=> $id_documentos
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
    private function errors(bool $edit = false, int $id = -1){

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
        $this->numero_cuenta = $http->request->get('numero_cuenta');
        $this->tipo_cliente = $http->request->get('tipo_cliente');

        $this->id_comercio = $this->db->scape($http->request->get('id_comercio'));
        $this->id_sucursal = $this->db->scape($http->request->get('id_sucursal'));


        # Verificar que no están vacíos
        if (Helper\Functions::e($this->primer_nombre, $this->primer_apellido,$this->telefono)) {
            throw new ModelsException('Todos los campos marcados con "*" son necesarios.');
        }

        if (Helper\Functions::e($this->pass,$this->pass2,$this->usuario,$this->email) and !$edit) {
            throw new ModelsException('Todos los campos marcados con "*" son necesarios.');
        }

        if ($edit) {
            $id_user=$this->db->scape($http->request->get('id_user'));
            $this->email =  $this->db->select("email","users",null,"id_user=$id_user")[0]["email"];
        }
        
        if ( !ctype_alpha($this->primer_nombre) or !ctype_alpha($this->primer_apellido) ) {
            throw new ModelsException('Los nombres y apellidos no pueden poseer números, símbolos o espacios.');
        }

        if ( !Helper\Functions::emp($this->segundo_nombre) && !ctype_alpha($this->segundo_nombre)) {
            throw new ModelsException('Los nombres y apellidos no pueden poseer números, símbolos o espacios.');
        }

        if ( !Helper\Functions::emp($this->segundo_apellido) && !ctype_alpha($this->segundo_apellido)) {
            throw new ModelsException('Los nombres y apellidos no pueden poseer números, símbolos o espacios.');
        }

        if ( !ctype_alnum($this->usuario) and !$edit) {
            throw new ModelsException('El usuario no puede poseer símbolos ni espacios .');
        }

        if (!ctype_digit($this->telefono)){
            throw new ModelsException("Teléfono inválido, debe ser numérico.");              
        }

        if (strlen($this->telefono) < 11){
            throw new ModelsException("Telefono invalido, debe tener al menos 11 digitos");              
        }

        if( Helper\Functions::emp($this->numero_cuenta) ){
            throw new ModelsException('Debe introducir un número de cuenta');
        }
        
        if ( strlen($this->numero_cuenta) != 20  ){
            throw new ModelsException("Número de cuenta inválido.");              
        }

        $nro_cuenta = $this->db->scape($this->numero_cuenta);
        $consulta = $this->db->select("email","users",null,"numero_cuenta='$nro_cuenta'");
        if ( false != $consulta and ($consulta[0]["email"] != $this->email) ){
            throw new ModelsException("El número de cuenta ya esta asociado a otro usuario.");              
        }

        # Veriricar contraseñas, email y nombre de usuario
        if (!$edit) {           
            $this->checkPassMatch($this->pass, $this->pass2);
            $this->checkEmail($this->email);

           #Revisa la exitencia del nombre de usuario que se está introduciendo
           if($this->checkUsuario($this->usuario) != null){
               throw new ModelsException('El nombre de usuario ya esta en uso.'); 
           }
       }

        if($this->sexo!="m" and $this->sexo!="f") {
            throw new ModelsException('Sexo no válido.');
        }

        if($this->tipo!=0 and $this->tipo!=1 and $this->tipo!=2 and $this->tipo!=3) {
            throw new ModelsException('Tipo de usuario no válido.');
        }

        if($this->tipo == 2 and $this->tipo_cliente!=='Simple' and $this->tipo_cliente!=='Medio' and $this->tipo_cliente!=='Premiun') {
            throw new ModelsException('Tipo de cliente no válido.');
        }

        if( ($this->tipo == 1)  or ($this->tipo == 3)  ){
            if(!Helper\Functions::emp($this->id_comercio) && !Helper\Functions::emp($this->id_sucursal)){
                throw new ModelsException('El usuario solo puede pertenecer a una sucursal o a un comercio.');
            }

            if(Helper\Functions::emp($this->id_comercio) && Helper\Functions::emp($this->id_sucursal)){
                throw new ModelsException('Debe seleccionar una sucursal o comercio.');
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
            'numero_cuenta' => $this->numero_cuenta,
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

            #Porque el tipo puede ser cero
            $data['tipo'] = $this->tipo;
  
            #Si es un cliente será un cliente simple
            if( $this->tipo==2 ){
                $data['tipo_cliente']="Simple";
            }

            # Registrar al usuario
            $id_user =  $this->db->insert('users',$data);

            #Se relaciona con sucursal o comercio si es un vendedor o supervisor
            if (  ($this->tipo == 1)  or ($this->tipo == 3)  ){
                if (!Helper\Functions::emp($this->id_sucursal)){
                    $this->db->update('users', array('id_sucursal'=>$this->id_sucursal), "id_user = $id_user");
                }else{
                    $this->db->update('users', array('id_comercio_afiliado'=>$this->id_comercio), "id_user = $id_user");
                }          
            }
            

            /*
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
            ), "id_user = '$id_user'");*/
            

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

            $id_user = $http->request->get('id_user');

            $this->errors(true,$id_user);

            /*if($this->tipo == 0 ){
                $this->tipo = 2;
            }*/

            $u = array(
                'primer_nombre' => $this->primer_nombre,
                'segundo_nombre' => $this->segundo_nombre,
                'primer_apellido' => $this->primer_apellido,
                'segundo_apellido' => $this->segundo_apellido,
                'sexo' => $this->sexo,
                'telefono' => $this->telefono,
                'numero_cuenta' => $this->numero_cuenta,
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

            #Porque el tipo puede ser cero
            $data['tipo'] = $this->tipo;
            
            #Si es un cliente 
            if( $this->tipo==2 ){
                $data['tipo_cliente']=$this->tipo_cliente;
            }else{
                $this->db->real_query("UPDATE users SET tipo_cliente = NULL WHERE id_user = '$id_user'");
            }
            
            #Edita un usuario
             if ( ($this->tipo == 1) or ($this->tipo == 3)  ){
                if (!Helper\Functions::emp($this->id_sucursal)){
                    $this->db->real_query("UPDATE users SET id_comercio_afiliado = NULL WHERE id_user = '$id_user'");
                    $data['id_sucursal']=$this->id_sucursal;
                    $this->db->update('users', $data, "id_user = '$id_user'");
                }
                else{
                    $this->db->real_query("UPDATE users SET id_sucursal = NULL WHERE id_user = '$id_user'");
                    $data['id_comercio_afiliado']=$this->id_comercio;
                    $this->db->update('users', $data, "id_user = '$id_user'");
                }
            }else{
                $this->db->real_query("UPDATE users SET id_comercio_afiliado = NULL WHERE id_user = '$id_user'");
                $this->db->real_query("UPDATE users SET id_sucursal = NULL WHERE id_user = '$id_user'");
                $this->db->update('users', $data, "id_user = '$id_user'");
            }


            return array('success' => 1, 'message' => 'Usuario editado con éxito!');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }


    /**
     * Sube documentos de de un usuario
     */
    public function UploadDocuments(){
        try {
        
            global $http,$config;

            #Obtiene los documentos del formulario
            $fotos = $_FILES;

            #Carga del documento de identidad
            if (array_key_exists('foto_documento',$fotos)) {

                if ($fotos["foto_documento"]["error"] != 0) {
                    throw new ModelsException('Hubo un error cargando documento de identidad, intente de nuevo.');
                }

                if ( !strpos($fotos["foto_documento"]["type"], "jpeg") and !strpos($fotos["foto_documento"]["type"], "jpg") and
                !strpos($fotos["foto_documento"]["type"], "png") ){
                    throw new ModelsException('El documento de identidad debe ser una imagen.');
                }

            }

            #Carga del pasaporte
            if (array_key_exists('foto_pasaporte',$fotos)) {

                if ($fotos["foto_pasaporte"]["error"] != 0) {
                    throw new ModelsException('Hubo un error cargando el pasaporte, intente de nuevo.');
                }

                if ( !strpos($fotos["foto_pasaporte"]["type"], "jpeg") and !strpos($fotos["foto_pasaporte"]["type"], "jpg") and
                !strpos($fotos["foto_pasaporte"]["type"], "png") ){
                    throw new ModelsException('El pasaporte debe ser una imagen.');
                }

            }

            #Carga del rif
            if (array_key_exists('foto_rif',$fotos)) {

                if ($fotos["foto_rif"]["error"] != 0) {
                    throw new ModelsException('Hubo un error cargando el rif, intente de nuevo.');
                }

                if ( !strpos($fotos["foto_rif"]["type"], "jpeg") and !strpos($fotos["foto_rif"]["type"], "jpg") and
                !strpos($fotos["foto_rif"]["type"], "png") ){
                    throw new ModelsException('El rif debe ser una imagen.');
                }

            }

            #Carga de la referencia bancaria 1
            if (array_key_exists('foto_ref1',$fotos)) {

                if ($fotos["foto_ref1"]["error"] != 0) {
                    throw new ModelsException('Hubo un error cargando la primera referencia bancaria, intente de nuevo.');
                }

                if ( !strpos($fotos["foto_ref1"]["type"], "jpeg") and !strpos($fotos["foto_ref1"]["type"], "jpg") and
                !strpos($fotos["foto_ref1"]["type"], "png") ){
                    throw new ModelsException('La primera referencia bancaria debe ser una imagen.');
                }

            }

            #Carga de la referencia bancaria 2
            if (array_key_exists('foto_ref2',$fotos)) {

                if ($fotos["foto_ref2"]["error"] != 0) {
                    throw new ModelsException('Hubo un error cargando la segunda referencia bancaria intente de nuevo.');
                }

                if ( !strpos($fotos["foto_ref2"]["type"], "jpeg") and !strpos($fotos["foto_ref2"]["type"], "jpg") and
                !strpos($fotos["foto_ref2"]["type"], "png") ){
                    throw new ModelsException('La segunda referencia bancaria debe ser una imagen.');
                }

            }


            $path = "../";        
            $id_user = $this->getOwnerUser()["id_user"];   

                #Si se cargó la foto del documento de identidad se guarda
                $dir_foto_documento=null;
                if (array_key_exists('foto_documento',$fotos)) {               
                    $dir_foto_documento = "views/img/documentos/usuarios/documentoidentidad".$id_user.".png";
                            
                    $tmp_name = $fotos["foto_documento"]["tmp_name"];
                    // basename() puede evitar ataques de denegación de sistema de ficheros;
                    // podría ser apropiada más validación/saneamiento del nombre del fichero
                    $name = basename($fotos["foto_documento"]["name"]);
                    move_uploaded_file($tmp_name, "$path"."$dir_foto_documento");
                        
                }

                #Si se cargó la foto del pasaporte se guarda
                $dir_pasaporte=null;
                if (array_key_exists('foto_pasaporte',$fotos)) {               
                    $dir_pasaporte = "views/img/documentos/usuarios/pasaporte".$id_user.".png";
                            
                    $tmp_name = $fotos["foto_pasaporte"]["tmp_name"];
                    // basename() puede evitar ataques de denegación de sistema de ficheros;
                    // podría ser apropiada más validación/saneamiento del nombre del fichero
                    $name = basename($fotos["foto_pasaporte"]["name"]);
                    move_uploaded_file($tmp_name, "$path"."$dir_pasaporte");
                        
                }

                #Si se cargó la foto del rif se guarda
                $dir_rif=null;
                if (array_key_exists('foto_rif',$fotos)) {               
                    $dir_rif = "views/img/documentos/usuarios/rif".$id_user.".png";
                            
                    $tmp_name = $fotos["foto_rif"]["tmp_name"];
                    // basename() puede evitar ataques de denegación de sistema de ficheros;
                    // podría ser apropiada más validación/saneamiento del nombre del fichero
                    $name = basename($fotos["foto_rif"]["name"]);
                    move_uploaded_file($tmp_name, "$path"."$dir_rif");
                        
                }

                 #Si se cargó la foto de la primera referencia bancaria se guarda
                 $dir_ref1=null;
                 if (array_key_exists('foto_ref1',$fotos)) {               
                     $dir_ref1 = "views/img/documentos/usuarios/primerareferenciabancaria".$id_user.".png";
                             
                     $tmp_name = $fotos["foto_ref1"]["tmp_name"];
                     // basename() puede evitar ataques de denegación de sistema de ficheros;
                     // podría ser apropiada más validación/saneamiento del nombre del fichero
                     $name = basename($fotos["foto_ref1"]["name"]);
                     move_uploaded_file($tmp_name, "$path"."$dir_ref1");
                         
                 }

                #Si se cargó la foto de la segunda referencia bancaria se guarda
                $dir_ref2=null;
                if (array_key_exists('foto_ref2',$fotos)) {               
                    $dir_ref2 = "views/img/documentos/usuarios/segundareferenciabancaria".$id_user.".png";
                            
                    $tmp_name = $fotos["foto_ref2"]["tmp_name"];
                    // basename() puede evitar ataques de denegación de sistema de ficheros;
                    // podría ser apropiada más validación/saneamiento del nombre del fichero
                    $name = basename($fotos["foto_ref2"]["name"]);
                    move_uploaded_file($tmp_name, "$path"."$dir_ref2");
                        
                }

                $id_documentos = $this->db->select("id_documentos","users",null,"id_user=$id_user")[0]["id_documentos"];

                $data = array(
                    'documento_identidad' =>  $dir_foto_documento == null ? null : $config['build']['url'] . $dir_foto_documento,
                    'pasaporte' => $dir_pasaporte == null ? null : $config['build']['url'] . $dir_pasaporte,
                    'rif' => $dir_rif == null ? null : $config['build']['url'] . $dir_rif,
                    'referencia_bancaria_1' => $dir_ref1 == null ? null : $config['build']['url'] . $dir_ref1,
                    'referencia_bancaria_2' => $dir_ref2 == null ? null : $config['build']['url'] . $dir_ref2
                );

                #Se actualiza la db con la ruta de los documentos
                $this->db->update('documentos',$data, "id_documento = '$id_documentos'");

            return array('success' => 1, 'message' => 'Carga de imagenes realizada con éxito.');
        } catch (ModelsException $e) {
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

       $id_user = $this->db->scape($this->id);

       #Verifica si el usuario a borrar es una sucursal
       $id_sucursal = $this->db->select("id_sucursal","sucursal",null,"id_user='$id_user'");

       #En caso de existir una sucursal con el id de usuario la borra
        if($id_sucursal != false){
            $is = $id_sucursal[0]["id_sucursal"];
            $this->db->delete('sucursal',"id_sucursal='$is'",'1');
        }

        $this->db->delete('users',"id_user='$id_user'",'1');

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
        $inner = "LEFT JOIN sucursal ON sucursal.id_sucursal = users.id_sucursal
                  LEFT JOIN comercio_afiliado ON comercio_afiliado.id_comercio_afiliado = users.id_comercio_afiliado
                  LEFT JOIN documentos ON documentos.id_documento = users.id_documentos";
        return $this->db->select("users.$select,sucursal.nombre as ns, comercio_afiliado.nombre as can,
                                documentos.documento_identidad,documentos.pasaporte,documentos.rif,
                                documentos.referencia_bancaria_1,documentos.referencia_bancaria_2",'users',$inner,$where);
    }

    /**
     * Obtiene los documentos del usuario según la id
     * 
     *  @param string $id_documentos : Id del elemento que contiene los documentos de un usuario
     * 
     * @return false|array con información de los documentos
     */
    public function getDocumentosById($id_documentos){

        return $this->db->select("*","documentos",null,"id_documento='$id_documentos'");

    }

    /**
     * Trae los datos generales relacionados a las monedas
     */
    public function datosGenerales(){
        global $http;

        $email = $http->query->get('email');


        $id_user = ($this->db->select('id_user','users',null,"email='$email'"))[0]['id_user'];


        #Instancio el modelo monedas para buscar el ultimo precio del oro y la plata
        $m = new Model\Monedas;
        $ultimo_precio_oro = ($m->getPrice("oro"))[0][0];
        $ultimo_precio_plata = ($m->getPrice("plata"))[0][0];

        #Queries para buscar las monedas de los usuarios
        $inner = "INNER JOIN user_moneda ON user_moneda.codigo_moneda = moneda.codigo AND user_moneda.id_usuario = $id_user";
        $monedas_oro=$this->db->select('diametro,espesor,peso','moneda',$inner,"composicion='oro'");
        $monedas_plata=$this->db->select('diametro,espesor,peso','moneda',$inner,"composicion='plata'");
        
        #Contar el total de monedas, si no encontro nada volver la variable 0
        $total_monedas_oro = false !== $monedas_oro ? sizeof($monedas_oro):0;
        $total_monedas_plata = false !== $monedas_plata ? sizeof($monedas_plata):0;

        #Se calculo el total en dolares de las monedas
        $total_oro_dolares = 0;
        for ($i=0; $i < $total_monedas_oro ; $i++) { 
            
            $total_oro_dolares =$total_oro_dolares + ( $monedas_oro[$i]["peso"] * ($ultimo_precio_oro/28.3495) ); 

        }

        $total_plata_dolares = 0;
        for ($i=0; $i < $total_monedas_plata ; $i++) { 

            $total_plata_dolares =$total_plata_dolares + ( $monedas_plata[$i]["peso"] * ($ultimo_precio_plata/28.3495) );
            
        }

        return array(
            'total_monedas_oro' => $total_monedas_oro,
            'total_monedas_plata' => $total_monedas_plata,
            'total_oro_discriminado' => $total_oro_dolares,
            'total_plata_discriminado' => $total_plata_dolares,
            'balance_general' => $total_oro_dolares + $total_plata_dolares
        );
    }


    /**
     * __construct()
     */
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
		$this->startDBConexion();
    }
}