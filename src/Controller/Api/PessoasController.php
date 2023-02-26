<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Model\Entity\Pessoa;
use App\Model\Table\PessoasTable;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Database\Expression\QueryExpression;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\InternalErrorException;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PessoasController
 *
 * @property \App\Model\Table\PessoasTable $Pessoas
 */
class PessoasController extends AppController
{
    /**
     * Initialize controller
     *
     * @return void
     * @throws \Exception
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->Authentication->allowUnauthenticated([
            'login',
            'tipos',
            'add',
            'esqueceusenha',
            'reenviar',
        ]);

        $this->Crud->setConfig('actions', $this->Crud->normalizeArray([
            'Crud.Add',
            'Crud.Edit',
        ]));
    }

    /**
     * @return void
     */
    public function login()
    {
        $result = $this->Authentication->getResult();
        if ($result->isValid()) {
            if (!file_exists(CONFIG . 'jwt.key')) {
                throw new InternalErrorException('Chave privada do JWT não encontrada!');
            }
            $privateKey = file_get_contents(CONFIG . 'jwt.key');
            /** @var \App\Model\Entity\Pessoa $user */
            $user = $result->getData();
            $payload = [
                'iss' => 'Monte Negro',
                'sub' => $user->id,
                'iat' => time(),
                'painel' => $user->model,
            ];
            $json = [
                'token' => JWT::encode($payload, $privateKey, 'RS512'),
                'painel' => $user->model,
            ];
        } else {
            $clienteNaoAtivou = $this->Pessoas
                ->find()
                ->where(function (QueryExpression $expression) {
                    $expression
                        ->eq('Pessoas.email', $this->getRequest()->getData('email'))
                        ->eq('Pessoas.status', PessoasTable::AGUARDANDO_VALIDACAO);

                    return $expression;
                })
                ->count();
            // se cliente já se cadastrou e ainda não confirmou o email, lança um 403 ao invés de um 401
            $this->setResponse($this->getResponse()->withStatus($clienteNaoAtivou > 0 ? 403 : 401));
            $json = [];
        }
        $this->set(compact('json'));
        $this->viewBuilder()->setOption('serialize', 'json');
    }

    /**
     * Logout method
     *
     * @return \Cake\Http\Response|null|void Redirects to logout URL
     */
    public function logout()
    {
        $url = $this->Authentication->logout();

        $success = true;

        $this->set(compact('success', 'url'));
        $this->viewBuilder()->setOption('serialize', ['success', 'data' => 'url']);
    }

    /**
     * @return void
     */
    public function esqueceusenha()
    {
        $this->getRequest()->allowMethod('post');

        /** @var \App\Model\Entity\Pessoa|null $pessoa */
        $pessoa = $this->Pessoas
            ->find()
            ->where(function (QueryExpression $expression) {
                return $expression
                    ->eq('Pessoas.status', PessoasTable::ATIVO)
                    ->eq('Pessoas.email', $this->getRequest()->getData('email'));
            })
            ->first();

        if ($pessoa) {
            $this->QueuedJobs->createJob('EmailEsqueciSenha', [
                'pessoa_id' => $pessoa->id,
            ]);
        }

        $success = true;
        $this->set(compact('success'));
        $this->viewBuilder()->setOption('serialize', ['success']);
    }

    /**
     * Add method
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function add(): ResponseInterface
    {
        $auth = $this->Authentication->getIdentity();
        if ($auth instanceof Pessoa) {
            throw new ForbiddenException('Usuário já logado!');
        }

        $this->Crud->on('beforeSave', function (EventInterface $event) {
            /** @var \App\Model\Entity\Pessoa $entity */
            $entity = $event->getSubject()->entity;
            $entity->model = PessoasTable::CLIENTE;
            $entity->status = PessoasTable::AGUARDANDO_VALIDACAO;
        });

        $this->Crud->on('afterSave', function (EventInterface $event) {
            $subject = $event->getSubject();
            $success = $subject->success;

            // Envia email de ativação de conta
            if ($success) {
                $this->QueuedJobs->createJob('EmailCadastro', [
                    'cliente_id' => $subject->entity->id,
                ]);
            }
        });

        return $this->Crud->execute();
    }

    /**
     * Edit method
     *
     * @param int|string $id ID da pessoa
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function edit($id): ResponseInterface
    {
        $this->Crud->on('beforeFind', function (EventInterface $event) {
            $query = $event->getSubject()->query;

            $query
                ->contain([
                    'Enderecos' => [
                        'joinType' => 'LEFT',
                        'Cidades' => [
                            'joinType' => 'LEFT',
                            'Estados' => [
                                'joinType' => 'LEFT',
                            ],
                        ],
                    ],
                ]);
        });

        $this->Crud->on('afterFind', function (EventInterface $event) {
            /** @var \App\Model\Entity\Pessoa $pessoa */
            $pessoa = $event->getSubject()->entity;

            $this->Authorization->authorize($pessoa);
        });

        $this->Crud->on('beforeSave', function (EventInterface $event) {
            /** @var \App\Model\Entity\Pessoa $entity */
            $entity = $event->getSubject()->entity;
            $data = $this->getRequest()->getData('senha');

            if (!empty($data['senha'])) {
                if (empty($data['senha_atual'])) {
                    $senhaAtual = '';
                } else {
                    $senhaAtual = $data['senha_atual'];
                }

                $pessoa = $this->Pessoas->get($entity->id);
                $hasher = new DefaultPasswordHasher();

                if (!$hasher->check($senhaAtual, $pessoa->senha)) {
                    throw new BadRequestException('Senha atual inválida');
                }
            }
        });

        return $this->Crud->execute();
    }

    /**
     * Info method
     *
     * @return void
     * @throws \Exception
     */
    public function info(): void
    {
        $user = $this->Authentication->getIdentity();

        $pessoa = $this->Pessoas
            ->find()
            ->contain([
                'Enderecos' => [
                    'joinType' => 'LEFT',
                    'Cidades' => [
                        'joinType' => 'LEFT',
                        'Estados' => [
                            'joinType' => 'LEFT',
                        ],
                    ],
                ],
            ])
            ->where(function (QueryExpression $expression) use ($user) {
                return $expression->eq('Pessoas.id', $user->id);
            });

        $result = [
            'success' => true,
            'data' => $pessoa,
        ];

        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', 'result');
    }

    /**
     * Tipos method
     * Retorna os tipos de pessoa (física, juridica)
     *
     * @return void
     */
    public function tipos()
    {
        $tipos = [];
        foreach (PessoasTable::TIPOS as $tipo => $label) {
            $tipos[] = [
                'id' => $tipo,
                'label' => $label,
            ];
        }

        $success = true;

        $this->set(compact('success', 'tipos'));
        $this->viewBuilder()->setOption('serialize', ['success', 'data' => 'tipos']);
    }

    /**
     * Reenviar method
     * Reenvia o email de ativação para o cliente
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function reenviar()
    {
        if ($this->getRequest()->is('post')) {
            $email = $this->getRequest()->getData('email');

            /** @var \App\Model\Entity\Pessoa $cliente */
            $cliente = $this->Pessoas
                ->find()
                ->where(function (QueryExpression $expression) use ($email) {
                    $expression
                        ->eq('Pessoas.email', $email);

                    return $expression;
                })
                ->first();

            if (!empty($cliente) && $cliente->status === PessoasTable::AGUARDANDO_VALIDACAO) {
                $this->QueuedJobs->createJob('EmailCadastro', [
                    'cliente_id' => $cliente->id,
                ]);
            }
        }

        $success = true;
        $result = [];

        $this->set(compact('success', 'result'));
        $this->viewBuilder()->setOption('serialize', ['success', 'data' => 'result']);
    }

    /**
     * Salva token do firebase
     *
     * @return void
     */
    public function firebase()
    {
        $this->getRequest()->allowMethod('post');
        $token = $this->getRequest()->getData('token');

        /** @var \App\Model\Entity\Dispositivo $dispositivo */
        $dispositivo = $this->getRequest()->getAttribute('dispositivo');

        /** @var \App\Model\Entity\Dispositivo $dispositivoExiste */
        $dispositivoExiste = $this->Pessoas->Dispositivos
            ->find()
            ->where(function (QueryExpression $expression) use ($token) {
                $expression->eq('Dispositivos.firebase_token', $token);

                return $expression;
            })
            ->first();

        if (empty($dispositivoExiste)) {
            $dispositivo->pessoa_id = $this->Authentication->getIdentity()->getIdentifier();
            $dispositivo->firebase_token = $token;
            $dispositivo = $this->Pessoas->Dispositivos->save($dispositivo);
        } else {
            if ($dispositivoExiste->id != $dispositivo->id) {
                $this->Pessoas->Dispositivos->delete($dispositivoExiste);
            }
        }

        $result = [
            'success' => true,
            'id' => $dispositivo->id,
        ];

        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', 'result');
    }
}
