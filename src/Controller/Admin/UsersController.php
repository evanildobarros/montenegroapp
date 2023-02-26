<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Entity\User;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Client;
use Cake\Http\Exception\BadRequestException;
use Cake\Mailer\Mailer;
use Cake\Mailer\MailerAwareTrait;
use Cake\Routing\Router;
use Cake\Utility\Security;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @property \Search\Controller\Component\SearchComponent $Search
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    use MailerAwareTrait;

    /**
     * Initialize controller
     *
     * @return void
     * @throws \Exception
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Search.Search', [
            'actions' => [
                'index',
            ],
        ]);
        $this->Authentication->allowUnauthenticated(['login', 'recoverpassword', 'resetpassword']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Users
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ])
            ->contain(['Groups']);
        $users = $this->paginate($query);

        $isSearch = $this->Users->isSearch();
        $groups = $this->Users->Groups->find('list');
        $this->set(compact('users', 'isSearch', 'groups'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $conn = $this->Users->getConnection();
            try {
                $conn->begin();
                $user = $this->Users->patchEntity($user, $this->getRequest()->getData());
                $this->Users->saveOrFail($user);

                $conn->commit();
                $this->Flash->success(__('O usuário foi salvo com sucesso.'));

                return $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->logException($e);
                $this->Flash->error(__('O usuário não pode ser salvo. Por favor, tente novamente.'));
            }
        }
        $groups = $this->Users->Groups->find('list');
        $this->set(compact('user', 'groups'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => [],
        ]);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->Users->getConnection();
            try {
                $conn->begin();

                /** @var \App\Model\Entity\User $auth */
                $auth = $this->Authentication->getIdentity();
                if (($auth->id != 1 || $auth->username != 'leandro@winsite.com.br') && ($user->id != $auth->id)) {
                    throw new \Exception('Este usuário não pode ser alterado.');
                }

                $user = $this->Users->patchEntity($user, $this->getRequest()->getData());
                $this->Users->saveOrFail($user);

                $conn->commit();
                $this->Flash->success(__('O usuário foi salvo com sucesso.'));

                return $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->logException($e);
                $this->Flash->error(__('O usuário não pode ser salvo. Por favor, tente novamente.'));
            }
        }
        $user->unset('password');
        $groups = $this->Users->Groups->find('list');
        $this->set(compact('user', 'groups'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->Users->getConnection();
        try {
            $conn->begin();
            $user = $this->Users->get($id);

            if ($id == 1 || $user->username == 'leandro@winsite.com.br') {
                throw new \Exception('Este usuário não pode ser excluído.');
            }

            $this->Users->deleteOrFail($user);
            $conn->commit();
            $this->Flash->success(__('O usuário foi excluído com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->logException($e);
            $this->Flash->error(__('O usuário não pode ser excluído. Por favor, tente novamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * DeleteAll method
     *
     * @param string|null $ids User id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteAll($ids = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->Users->getConnection();
        try {
            $conn->begin();
            $this->Users
                ->find()
                ->where(function (QueryExpression $expression) use ($ids) {
                    $expression->in('Users.id', explode('|', $ids));

                    return $expression;
                })
                ->each(function (User $user) {
                    if ($user->id != 1 && $user->username != 'leandro@winsite.com.br') {
                        $this->Users->deleteOrFail($user);
                    }
                });
            $conn->commit();
            $this->Flash->success(__('O usuário foi excluído com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->logException($e);
            $this->Flash->error(__('O usuário não pode ser excluído. Por favor, tente novamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Toggle method
     *
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Http\Exception\BadRequestException
     */
    public function toggle()
    {
        if ($this->getRequest()->is('ajax')) {
            $id = $this->getRequest()->getData('id');
            $campo = $this->getRequest()->getData('field');

            $user = $this->Users
                ->find()
                ->select([
                    'id',
                    $campo,
                ])
                ->where(function (QueryExpression $expression) use ($id, $campo) {
                    $expression->eq('id', $id);

                    return $expression;
                })
                ->firstOrFail();

            $user = $this->Users->patchEntity($user, [
                $campo => !$user->get($campo),
            ]);

            $this->Users->saveOrFail($user);

            $this->set(compact('user'));
            $this->set('_serialize', ['user']);
        } else {
            throw new BadRequestException('Método inválido!');
        }
    }

    /**
     * Login method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function login()
    {
        $this->viewBuilder()->setLayout('login');
        $result = $this->Authentication->getResult();

        if ($this->getRequest()->is('post')) {
            $client = new Client();
            $response = $client->post(Configure::read('ReCaptcha.siteverify'), [
                'secret' => Configure::read('ReCaptcha.secret_key'),
                'response' => $this->getRequest()->getData('token'),
                'remoteip' => $this->getRequest()->clientIp(),
            ]);
            $resposta = json_decode($response->getBody()->getContents(), true);

            if (true) {
                if (!$result->isValid()) {
                    $this->Flash->error('Nome de usuário ou senha inválidos!');
                }
            } else {
                $this->Flash->error(__('Houve um erro ao validar o reCAPTCHA! Por favor, tente novamente.'));
            }
        }

        if ($result->isValid()) {
            $target = $this->Authentication->getLoginRedirect() ?? '/admin';

            return $this->redirect($target);
        }
    }

    /**
     * Logout method
     *
     * @return \Cake\Http\Response|null|void Redirects to logout URL
     */
    public function logout()
    {
        return $this->redirect($this->Authentication->logout());
    }

    /**
     * Recover Password method
     *
     * @return \Cake\Http\Response|null
     */
    public function recoverpassword()
    {
        $this->viewBuilder()->setLayout('login');

        if ($this->getRequest()->is('post')) {
            /** @var \App\Model\Entity\User $user */
            $user = $this->Users
                ->find()
                ->where([
                    'username' => $this->getRequest()->getData('username'),
                ])
                ->first();
            if (!empty($user)) {
                $conn = $this->Users->getConnection();
                try {
                    $conn->begin();
                    $user->token = Security::hash($user->username . time(), 'sha256', true);
                    $this->Users->saveOrFail($user);

                    // Enviar email
                    $mail = new Mailer('default');
                    $mail
                        ->setEmailFormat('html')
                        ->setTo($user->username)
                        ->setSubject('Monte Negro - Recuperação de senha');

                    $mail
                        ->setViewVars([
                            'user' => $user,
                            'link' => Router::url([
                                'controller' => 'users',
                                'action' => 'resetpassword',
                                $user->token,
                            ], true),
                        ])
                        ->viewBuilder()
                        ->setTemplate('Winsite.recoverpassword');

                    $mail->send();

                    $conn->commit();
                    $this->Flash->success(__(
                        'Aguarde em instantes você receberá um e-mail para redefinação de senha.'
                    ));

                    return $this->redirect(['action' => 'login']);
                } catch (\Exception $e) {
                    $conn->rollback();
                    $this->log($e->getMessage(), 'error');
                    $this->Flash->error(__('Por favor, tente novamente.'));
                }
            } else {
                $this->Flash->error(__('Usuário não encontrado!'));
            }
        }
    }

    /**
     *  Reset password method
     *
     * @param string $token Hash para pesquisa
     * @return \Cake\Http\Response|null|void
     */
    public function resetpassword($token)
    {
        $this->viewBuilder()->setLayout('login');

        if (empty($token)) {
            $this->Flash->error(__('Usuário inválido!'));

            return $this->redirect(['action' => 'login']);
        }
        $this->viewBuilder()->setLayout('login');
        $user = $this->Users->find()
            ->where(['token' => $token])
            ->first();
        if (empty($user)) {
            $this->Flash->error(__('Usuário não encontrado!'));

            return $this->redirect(['action' => 'login']);
        }
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->Users->getConnection();
            try {
                $conn->begin();
                $user = $this->Users->patchEntity($user, $this->getRequest()->getData());
                $user->token = null;
                $this->Users->saveOrFail($user);
                $conn->commit();
                $this->Flash->success(__('Senha alterada com sucesso.'));

                return $this->redirect(['action' => 'login']);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage(), 'error');
                $this->Flash->error(__('Não foi possível alterar sua senha. Por favor, tente novamente.'));
            }
        }

        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Dashboard method
     *
     * @return \Cake\Http\Response|null|void
     */
    public function dashboard()
    {
    }
}
