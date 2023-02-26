<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller\Api;

use Cake\Controller\Controller;
use Crud\Controller\ControllerTrait;

/**
 * Application Controller
 *
 * @property \Crud\Controller\Component\CrudComponent $Crud
 * @property \Queue\Model\Table\QueuedJobsTable $QueuedJobs
 * @property \App\Model\Table\ConfigsTable $Configs
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \Authorization\Controller\Component\AuthorizationComponent $Authorization
 */
class AppController extends Controller
{
    use ControllerTrait;

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Authorization.Authorization', [
            'skipAuthorization' => [
                'login',
            ],
        ]);
        $this->loadComponent('Authentication.Authentication');
        $this->loadModel('Queue.QueuedJobs');
        $this->loadComponent('Crud.Crud', [
            'listeners' => [
                'Crud.Api',
                'Crud.ApiPagination',
                'Crud.Search',
                'Crud.ApiQueryLog',
            ],
        ]);
        $this->loadModel('Queue.QueuedJobs');
        $this->loadModel('Configs');
    }
}
