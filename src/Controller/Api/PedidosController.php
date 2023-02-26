<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Hashids\Hashids;
use App\Model\Table\ObjetosTable;
use App\Model\Table\PagamentosTable;
use App\Model\Table\PedidosTable;
use App\Pagamentos\Cartao;
use Cake\Database\Expression\QueryExpression;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\Routing\Router;
use Crud\Error\Exception\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LogLevel;

/**
 * Class PedidosController
 *
 * @property \App\Model\Table\PedidosTable $Pedidos
 */
class PedidosController extends AppController
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

        $this->Crud->setConfig('actions', $this->Crud->normalizeArray([
            'Crud.Index',
            'Crud.View',
        ]));
    }

    /**
     * Index method
     * Retorna listagem de pedidos para os clientes
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function index()
    {
        $this->Crud->on('beforePaginate', function (EventInterface $event) {
            /** @var \Cake\ORM\Query $query */
            $query = $event->getSubject()->query;
            $query
                ->contain([
                    'EntregaMeios',
                ])
                ->select([
                    'Pedidos.id',
                    'Pedidos.status',
                    'Pedidos.meio_entrega',
                    'EntregaMeios.id',
                    'EntregaMeios.icone',
                ])
                ->orderDesc('Pedidos.created');

            $this->Authorization->applyScope($query);
        });

        return $this->Crud->execute();
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Exception
     */
    public function add()
    {
        $this->getRequest()->allowMethod('post');

        $pedido = $this->Pedidos->newEmptyEntity();
        $user = $this->Authentication->getIdentity();

        $conn = $this->Pedidos->getConnection();
        try {
            $conn->begin();
            $data = $this->getRequest()->getData();
            $this->log('API: Pedido iniciado', LogLevel::INFO, ['scope' => ['payments']]);

            // Pegando dados do cartão
            if (!isset($data['cartao'])) {
                throw new BadRequestException('Cartão não informado');
            }
            $dadosCartao = $data['cartao'];
            unset($data['cartao']);

            $data['status'] = PedidosTable::PENDENTE;
            $data['cliente_id'] = $user->id;

            if ($data['modalidade_distribuicao'] === PedidosTable::ENTREGA) {
                if (!isset($data['prazo_envio']) || empty($data['prazo_envio'])) {
                    throw new BadRequestException('O prazo de envio não pode ser vazio.');
                }
            }

            $this->log(
                'API: Dados recebidos do APP: ' . json_encode($data),
                LogLevel::INFO,
                ['scope' => ['payments']],
            );

            $this->log('API: Criado pedido', LogLevel::INFO, ['scope' => ['payments']]);
            $pedido = $this->Pedidos->patchEntity($pedido, $data, [
                'associated' => [
                    'Objetos' => [
                        'associated' => [
                            'EnderecoColetas',
                            'EnderecoEntregas',
                        ],
                    ],
                ],
            ]);
            $this->Pedidos->saveOrFail($pedido);
            $this->log(
                'API: Pedido salvo: ' . json_encode($pedido),
                LogLevel::INFO,
                ['scope' => ['payments']],
            );

            // Manipular dados do cartão
            $this->log('API: Criando cartão', LogLevel::INFO, ['scope' => ['payments']]);
            $cartao = new Cartao(
                $dadosCartao['numero'],
                $dadosCartao['bandeira'],
                $dadosCartao['mes'],
                $dadosCartao['ano'],
                $dadosCartao['cvv'],
            );
            $this->log('API: Cartão criado', LogLevel::INFO, ['scope' => ['payments']]);

            $this->log('API: Iniciado coleta do Cartão token', LogLevel::INFO, ['scope' => ['payments']]);
            $cartaoToken = $cartao->token();
            $senderHash = $cartao->senderHash();
            $this->log('API: Cartão token coletado', LogLevel::INFO, ['scope' => ['payments']]);

            $this->log('API: Criando pagamento', LogLevel::INFO, ['scope' => ['payments']]);
            $pagamento = $this->Pedidos->Pagamentos->newEntity([
                'pedido_id' => $pedido->id,
                'valor' => $pedido->valor_total,
                'status' => PagamentosTable::AGUARDANDO_PAGAMENTO,
            ]);
            $this->Pedidos->Pagamentos->saveOrFail($pagamento);
            $this->log(
                'API: Pagamento salvo: ' . json_encode($pagamento),
                LogLevel::INFO,
                ['scope' => ['payments']],
            );

            $this->QueuedJobs->createJob('CobrarPagamento', [
                'pagamento_id' => $pagamento->id,
                'ip' => $this->getRequest()->clientIp(),
                'senderHash' => $senderHash,
                'token' => $cartaoToken,
                'cardNome' => $dadosCartao['nome'],
                'cardCpf' => $dadosCartao['cpf'],
                'cardDataNascimento' => $dadosCartao['data_nascimento'],
            ]);
            $this->log('API: Criado Job para cobrar pagamento', LogLevel::INFO, ['scope' => ['payments']]);

            $this->QueuedJobs->createJob('EmailNovoPedidoCliente', [
                'pedido_id' => $pedido->id,
            ]);
            $this->QueuedJobs->createJob('EmailNovoPedidoAdmin', [
                'pedido_id' => $pedido->id,
            ]);
            $this->log('API: Criado Job para email novo pedido', LogLevel::INFO, ['scope' => ['payments']]);

            // Adiciona atualização do pedido
            $atualizacao = [
                'pedido_id' => $pedido->id,
                'titulo' => 'Pedido realizado',
                'descricao' => '',
                'data' => new FrozenTime(),
            ];
            $this->log(
                'API: Criando atualização: ' . json_encode($atualizacao),
                LogLevel::INFO,
                ['scope' => ['payments']],
            );
            $this->Pedidos->Atualizacoes->add($atualizacao);
            $this->log(
                'API: Atualização salva: ' . json_encode($atualizacao),
                LogLevel::INFO,
                ['scope' => ['payments']],
            );

            $conn->commit();
        } catch (PersistenceFailedException $e) {
            $conn->rollback();
            $this->log('Erro em adicionar solicitação: ' . $e->getMessage());
            throw new ValidationException($e->getEntity());
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->log(
                'API: Erro ao adicionar pedido: ' . $e->getMessage(),
                LogLevel::ERROR,
                ['scope' => ['payments']],
            );

            throw $e;
        }

        $result = [
            'success' => true,
            'data' => $pedido,
        ];

        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', 'result');
    }

    /**
     * View method
     *
     * @param int|string $id ID do pedido
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function view($id): ResponseInterface
    {
        $this->Crud->on('beforeFind', function (EventInterface $event) {
            $query = $event->getSubject()->query;

            $query
                ->contain([
                    'Objetos' => [
                        'EnderecoEntregas' => [
                            'Cidades' => [
                                'Estados',
                            ],
                        ],
                        'EnderecoColetas' => [
                            'Cidades' => [
                                'joinType' => 'LEFT',
                                'Estados' => [
                                    'joinType' => 'LEFT',
                                ],
                            ],
                        ],
                    ],
                    'Filiais',
                    'Atualizacoes',
                    'Pagamentos',
                ]);
        });

        $this->Crud->on('afterFind', function (EventInterface $event) {
            /** @var \App\Model\Entity\Pedido $pedido */
            $pedido = $event->getSubject()->entity;

            $this->Authorization->authorize($pedido);

            $pedido->codigo_rastreio = Hashids::getInstance()->encode($pedido->id);
            $pedido->etiqueta_url = Router::url([
                'controller' => 'pdf',
                'action' => 'etiqueta',
                $pedido->id,
                '_ext' => 'pdf',
                'prefix' => false,
            ], true);

            $tentativasRealizadas = $this->Pedidos->RotaPedidos->Tentativas
                ->find()
                ->join([
                    'RotaPedidos' => [
                        'table' => 'rota_pedidos',
                        'type' => 'INNER',
                        'conditions' => 'Tentativas.rota_pedido_id = RotaPedidos.id',
                    ],
                    'Pedidos' => [
                        'table' => 'pedidos',
                        'type' => 'INNER',
                        'conditions' => 'RotaPedidos.pedido_id = Pedidos.id',
                    ],
                ])
                ->where(function (QueryExpression $expression) use ($pedido) {
                    $expression
                        ->eq('Pedidos.id', $pedido->id);

                    return $expression;
                })
                ->count();

            $quantidadeTentativas = (int)$this->Configs->parametro('quantidade_tentativas');

            if ($tentativasRealizadas >= $quantidadeTentativas) {
                $pedido->tentativas_limite = true;
            } else {
                $pedido->tentativas_limite = false;
            }
        });

        return $this->Crud->execute();
    }

    /**
     * ModalidadesDistribuicao method
     * Retorna a lista de modalidades de distribuição
     *
     * @return void
     * @throws \Exception
     */
    public function modalidadesDistribuicao()
    {
        $this->getRequest()->allowMethod('get');

        $modalidades = [];
        foreach (PedidosTable::MODALIDADE_DISTRIBUICAO as $modalidade => $label) {
            $modalidades[] = [
                'id' => $modalidade,
                'label' => $label,
            ];
        }

        $result = [
            'success' => true,
            'data' => $modalidades,
        ];

        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', 'result');
    }

    /**
     * Prazoenvio method
     * Retorna o prazo de envio para pedidos do tipo modalidade_distribuição coleta
     *
     * @return void
     * @throws \Exception
     */
    public function prazoEnvio()
    {
        $this->getRequest()->allowMethod('get');

        $dias = (int)$this->Configs->parametro('prazo_envio');
        $dataBase = new FrozenDate();
        $prazoEnvio = $dataBase->addDays($dias);

        $result = [
            'success' => true,
            'data' => $prazoEnvio,
        ];

        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', 'result');
    }

    /**
     * unidadesPesos method
     * Retorna as unidades de peso disponíveis
     *
     * @return void
     */
    public function unidadesPesos()
    {
        $this->getRequest()->allowMethod('get');

        $unidades = [];
        foreach (ObjetosTable::UNIDADE_MEDIDA_PESO as $unidade => $label) {
            $unidades[] = [
                'id' => $unidade,
                'label' => $label,
            ];
        }

        $result = [
            'success' => true,
            'data' => $unidades,
        ];

        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', 'result');
    }

    /**
     * unidadesComprimento method
     * Retorna as unidades de comprimento disponíveis
     *
     * @return void
     */
    public function unidadesComprimento()
    {
        $this->getRequest()->allowMethod('get');

        $unidades = [];
        foreach (ObjetosTable::UNIDADE_MEDIDA_COMPRIMENTO as $unidade => $label) {
            $unidades[] = [
                'id' => $unidade,
                'label' => $label,
            ];
        }

        $result = [
            'success' => true,
            'data' => $unidades,
        ];

        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', 'result');
    }

    /**
     * Classificacoes method
     * Retorna a classificação do objeto
     *
     * @return void
     */
    public function classificar()
    {
        $this->getRequest()->allowMethod('get');
        $params = $this->getRequest()->getQueryParams();

        $altura = $params['altura'];
        $largura = $params['largura'];
        $profundidade = $params['profundidade'];
        $unidade_medida_comprimento = $params['unidade_medida_comprimento'];

        $classificacao = $this->Configs->classificacao($altura, $largura, $profundidade, $unidade_medida_comprimento);

        $result = [
            'success' => true,
            'data' => [
                'id' => $classificacao,
                'label' => ObjetosTable::CLASSIFICACAO[$classificacao],
            ],
        ];

        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', 'result');
    }

    /**
     * Recusado method
     * Retorna o motivo pelo qual o pedido foi recusado pelo gateway de pagamento
     *
     * @param string|int $id Id do pedido
     * @return void|null Return
     */
    public function recusado($id)
    {
        $pedido = $this->Pedidos
            ->find()
            ->where(function (QueryExpression $expression) use ($id) {
                $expression
                    ->eq('Pedidos.status', PedidosTable::CANCELADO)
                    ->eq('Pedidos.id', $id);

                return $expression;
            })
            ->firstOrFail();

        $this->Authorization->authorize($pedido);

        $pagamentos = $this->Pedidos->Pagamentos
            ->find()
            ->select([
                'Pagamentos.id',
                'Pagamentos.pedido_id',
                'Pagamentos.comentario',
                'Pagamentos.status',
                'Pagamentos.created',
            ])
            ->where(function (QueryExpression $expression) use ($pedido) {
                $expression
                    ->in('Pagamentos.status', [
                        PagamentosTable::EM_DISPUTA,
                        PagamentosTable::DEVOLVIDA,
                        PagamentosTable::CANCELADA,
                    ])
                    ->eq('Pagamentos.pedido_id', $pedido->id);

                return $expression;
            })
            ->orderDesc('Pagamentos.created')
            ->firstOrFail();

        $results = [
            'success' => true,
            'data' => $pagamentos,
        ];

        $this->set(compact('results'));
        $this->set('_serialize', ['results']);
    }

    /**
     * status method
     * Retorna os status do pedido
     *
     * @return void
     */
    public function status()
    {
        $status = [];
        foreach (PedidosTable::STATUS as $id => $label) {
            $status[] = [
                'id' => $id,
                'label' => $label,
            ];
        }

        $result = [
            'success' => true,
            'data' => $status,
        ];

        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', 'result');
    }
}
