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

/**
 * Modelo Registro
 */
class Registro extends Models implements IModels {
    use DBModel;

    /**
     * Respuesta generada por defecto para el endpoint
     * 
     * @return array
    */ 
    public function foo() : array {
        try {
            global $http;
                    
            return array('success' => 0, 'message' => 'Funcionando');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }

    /**
     * Retorna un string con los términos y condiciones
     */
    public function getTerminos() {

        return "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Suscipit deserunt quos at, eius quis ratione nihil error est, numquam temporibus doloremque recusandae, praesentium eligendi dolore tenetur itaque dignissimos! Nesciunt velit sint eum ad, labore recusandae eos consequatur aut. Nemo, eum, beatae. Possimus molestiae maiores delectus fugit distinctio autem praesentium neque eum excepturi earum dolor ullam eos enim, dignissimos, voluptatum aperiam sapiente ad soluta consequuntur molestias maxime dolores veritatis odit. Quo aut rem amet dolorum asperiores eius, nam a itaque quaerat, eos dolorem sapiente cupiditate neque enim vitae, autem natus laborum ullam odio recusandae. Sit maiores similique neque quas voluptatum labore exercitationem nesciunt, consequatur minus! Veritatis asperiores magni rem harum, facere non. Aspernatur quasi officia facilis eum quod minima obcaecati, soluta rerum, provident reprehenderit veniam, eligendi nesciunt nam ipsa nisi, iusto cum voluptas doloremque unde dolores deserunt. Laudantium minima, accusamus a, veritatis, fugit ducimus sint omnis iusto debitis optio neque! Perferendis deserunt, assumenda repellendus illum dolor, itaque aliquid eum. Repudiandae, provident! Doloribus hic necessitatibus, magnam natus consectetur debitis, nemo in aliquam libero veniam corporis officia itaque tempore iusto expedita doloremque commodi corrupti nesciunt neque reprehenderit id, illum earum nisi consequatur! Officia sunt, corrupti sit exercitationem facere, aperiam totam fuga autem, recusandae temporibus quasi excepturi, vitae facilis quaerat culpa sapiente magnam laborum soluta accusantium. Repudiandae dicta iure distinctio enim neque debitis alias omnis, porro unde odit, minima sit sunt nisi. In autem quis a eos debitis ad, quia distinctio cum unde neque! Natus aperiam corporis quos voluptatum, iusto aliquam, rem maiores fugit labore, non totam, quasi facilis quod repudiandae quae sunt a qui ex vel. In totam, soluta dignissimos repellendus, culpa blanditiis facilis voluptatibus deserunt vel quasi eveniet, cupiditate odio neque necessitatibus. Pariatur reiciendis optio tempore eum voluptatibus asperiores porro dolore dolor, iure et, eligendi minus exercitationem, ducimus fugiat quaerat obcaecati similique!";
    }

    /**
     * __construct()
    */
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
		$this->startDBConexion();
    }
}