<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Entity\Pedido;
use App\Model\Table\ObjetosTable;
use App\Model\Table\PedidosTable;
use App\Model\Table\RotaPedidosTable;
use App\Model\Table\RotasTable;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Exception\BadRequestException;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\ORM\Query;

/**
 * Pedidos Controller
 *
 * @property \App\Model\Table\PedidosTable $Pedidos
 * @property \Search\Controller\Component\SearchComponent $Search
 * @method \App\Model\Entity\Pedido[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
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

        $this->loadComponent('Search.Search', [
            'actions' => [
                'index',
                'financeiro',
                'resumo',
                'aguardandoObjetos',
                'aguardandoColetas',
                'tratativasTodas',
                'tratativasAndamento',
            ],
        ]);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Pedidos
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ])
            ->contain(['Pessoas', 'Objetos', 'Filiais', 'EntregaMeios']);
        $pedidos = $this->paginate($query, [
            'order' => [
                'id' => 'DESC',
            ],
        ]);

        $requestQuery = $this->getRequest()->getQueryParams();
        $clientes = [];
        if (isset($requestQuery['cliente_id'])) {
            $clientes = $this->Pedidos->Pessoas
                ->listaClientes()
                ->where(['Pessoas.id' => $requestQuery['cliente_id']])
                ->toArray();
        }
        $cidades = [];
        if (isset($requestQuery['cidade_id'])) {
            $cidades = $this->Pedidos->Objetos->EnderecoColetas->Cidades
                ->listaCidades()
                ->where(function (QueryExpression $expression) use ($requestQuery) {
                    return $expression->eq('Cidades.id', $requestQuery['cidade_id']);
                })
                ->toArray();
        }

        $classificacoes = ObjetosTable::CLASSIFICACAO;
        $unidades_medidas_comprimento = ObjetosTable::UNIDADE_MEDIDA_COMPRIMENTO;
        $unidades_medidas_peso = ObjetosTable::UNIDADE_MEDIDA_PESO;
        $filiais = $this->Pedidos->Filiais->listaAtivas()->toArray();
        $entregaMeios = $this->Pedidos->EntregaMeios->listaAtivas()->toArray();
        $modalidadeDistribuicao = PedidosTable::MODALIDADE_DISTRIBUICAO;
        $status = PedidosTable::STATUS;
        $isSearch = $this->Pedidos->isSearch();
        $this->set(compact(
            'pedidos',
            'clientes',
            'cidades',
            'classificacoes',
            'unidades_medidas_comprimento',
            'unidades_medidas_peso',
            'filiais',
            'entregaMeios',
            'modalidadeDistribuicao',
            'status',
            'isSearch',
        ));
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function financeiro()
    {
        $query = $this->Pedidos
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ])
            ->contain(['Pessoas', 'Objetos', 'Filiais', 'EntregaMeios']);
        $pedidos = $this->paginate($query, [
            'order' => [
                'id' => 'DESC',
            ],
        ]);

        $requestQuery = $this->getRequest()->getQueryParams();
        $clientes = [];
        if (isset($requestQuery['cliente_id'])) {
            $clientes = $this->Pedidos->Pessoas
                ->listaClientes()
                ->where(['Pessoas.id' => $requestQuery['cliente_id']])
                ->toArray();
        }
        $cidades = [];
        if (isset($requestQuery['cidade_id'])) {
            $cidades = $this->Pedidos->Objetos->EnderecoColetas->Cidades
                ->listaCidades()
                ->where(function (QueryExpression $expression) use ($requestQuery) {
                    return $expression->eq('Cidades.id', $requestQuery['cidade_id']);
                })
                ->toArray();
        }

        $classificacoes = ObjetosTable::CLASSIFICACAO;
        $unidades_medidas_comprimento = ObjetosTable::UNIDADE_MEDIDA_COMPRIMENTO;
        $unidades_medidas_peso = ObjetosTable::UNIDADE_MEDIDA_PESO;
        $filiais = $this->Pedidos->Filiais->listaAtivas()->toArray();
        $entregaMeios = $this->Pedidos->EntregaMeios->listaAtivas()->toArray();
        $modalidadeDistribuicao = PedidosTable::MODALIDADE_DISTRIBUICAO;
        $status = PedidosTable::STATUS;
        $isSearch = $this->Pedidos->isSearch();
        $this->set(compact(
            'pedidos',
            'clientes',
            'cidades',
            'classificacoes',
            'unidades_medidas_comprimento',
            'unidades_medidas_peso',
            'filiais',
            'entregaMeios',
            'modalidadeDistribuicao',
            'status',
            'isSearch',
        ));
    }

    /**
     * Resumo method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function resumo()
    {
        $query = $this->Pedidos
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ]);
        $pedidos = $this->paginate($query, [
            'order' => [
                'id' => 'DESC',
            ],
        ]);

        $requestQuery = $this->getRequest()->getQueryParams();
        $clientes = [];
        if (isset($requestQuery['cliente_id'])) {
            $clientes = $this->Pedidos->Pessoas
                ->listaClientes()
                ->where(['Pessoas.id' => $requestQuery['cliente_id']])
                ->toArray();
        }
        $cidades = [];
        if (isset($requestQuery['cidade_id'])) {
            $cidades = $this->Pedidos->Objetos->EnderecoColetas->Cidades
                ->listaCidades()
                ->where(function (QueryExpression $expression) use ($requestQuery) {
                    return $expression->eq('Cidades.id', $requestQuery['cidade_id']);
                })
                ->toArray();
        }

        $classificacoes = ObjetosTable::CLASSIFICACAO;
        $unidades_medidas_comprimento = ObjetosTable::UNIDADE_MEDIDA_COMPRIMENTO;
        $unidades_medidas_peso = ObjetosTable::UNIDADE_MEDIDA_PESO;
        $filiais = $this->Pedidos->Filiais->listaAtivas()->toArray();
        $entregaMeios = $this->Pedidos->EntregaMeios->listaAtivas()->toArray();
        $modalidadeDistribuicao = PedidosTable::MODALIDADE_DISTRIBUICAO;
        $status = PedidosTable::STATUS;
        $isSearch = $this->Pedidos->isSearch();
        $this->set(compact(
            'pedidos',
            'clientes',
            'cidades',
            'classificacoes',
            'unidades_medidas_comprimento',
            'unidades_medidas_peso',
            'filiais',
            'entregaMeios',
            'modalidadeDistribuicao',
            'status',
            'isSearch',
        ));
    }

    /**
     * TratativasAndamento method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function tratativasAndamento()
    {
        $limite_tentativas = (int)$this->Configs->parametro('quantidade_tentativas');

        $query = $this->Pedidos
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ])
            ->select([
                'Pedidos.id',
                'Pedidos.status',
                'RotaPedidos.rota_id',
                'RotaPedidos.tipo',
                'tentativas_realizadas' => 'COUNT(Tentativas.id)',
            ])
            ->join([
                'RotaPedidos' => [
                    'table' => 'rota_pedidos',
                    'type' => 'INNER',
                    'conditions' => 'Pedidos.id = RotaPedidos.pedido_id',
                ],
                'Tentativas' => [
                    'table' => 'tentativas',
                    'type' => 'INNER',
                    'conditions' => 'RotaPedidos.id = Tentativas.rota_pedido_id',
                ],
            ])
            ->where([
                'OR' => [
                    '(Pedidos.data_tratativa_coleta IS NULL AND RotaPedidos.tipo = \''
                    . RotaPedidosTable::COLETA . '\')',
                    '(Pedidos.data_tratativa_entrega IS NULL AND RotaPedidos.tipo = \''
                    . RotaPedidosTable::ENTREGA . '\')',
                ],
            ])
            ->group([
                'Pedidos.id',
                'Pedidos.status',
                'RotaPedidos.rota_id',
                'RotaPedidos.tipo',
            ])
            ->having(['tentativas_realizadas >=' => $limite_tentativas]);
        $pedidos = $this->paginate($query);

        $limite_tentativas = (int)$this->Configs->parametro('quantidade_tentativas');
        $status = PedidosTable::STATUS;
        $isSearch = $this->Pedidos->isSearch();
        $this->set(compact('pedidos', 'status', 'limite_tentativas', 'isSearch'));
    }

    /**
     * TratativasTodas method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function tratativasTodas()
    {
        $limite_tentativas = (int)$this->Configs->parametro('quantidade_tentativas');

        $query = $this->Pedidos
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ])
            ->select([
                'Pedidos.id',
                'Pedidos.status',
                'Pedidos.data_tratativa_coleta',
                'Pedidos.data_tratativa_entrega',
                'RotaPedidos.rota_id',
                'RotaPedidos.tipo',
                'tentativas_realizadas' => 'COUNT(Tentativas.id)',
            ])
            ->join([
                'RotaPedidos' => [
                    'table' => 'rota_pedidos',
                    'type' => 'INNER',
                    'conditions' => 'Pedidos.id = RotaPedidos.pedido_id',
                ],
                'Tentativas' => [
                    'table' => 'tentativas',
                    'type' => 'INNER',
                    'conditions' => 'RotaPedidos.id = Tentativas.rota_pedido_id',
                ],
            ])
            ->group([
                'Pedidos.id',
                'Pedidos.status',
                'Pedidos.data_tratativa_coleta',
                'Pedidos.data_tratativa_entrega',
                'RotaPedidos.rota_id',
                'RotaPedidos.tipo',
            ])
            ->having(['tentativas_realizadas >=' => $limite_tentativas]);
        $pedidos = $this->paginate($query);

        $limite_tentativas = (int)$this->Configs->parametro('quantidade_tentativas');
        $status = PedidosTable::STATUS;
        $isSearch = $this->Pedidos->isSearch();
        $this->set(compact('pedidos', 'status', 'limite_tentativas', 'isSearch'));
    }

    /**
     * aguardandoObjetos method
     * Pedidos pagos e que a modalidade de distribuição é COLETA
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function aguardandoObjetos()
    {
        $query = $this->Pedidos
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ])
            ->where(function (QueryExpression $expression) {

                // ADICIONAR CONDIÇÃO DE PAGAMENTO REALIZADO

                $expression
                    ->notIn('Pedidos.status', [
                        PedidosTable::PENDENTE,
                        PedidosTable::CANCELADO,
                        PedidosTable::FINALIZADO,
                    ])
                    ->isNull('Pedidos.data_chegada')
                    ->eq('Pedidos.modalidade_distribuicao', PedidosTable::ENTREGA);

                return $expression;
            })
            ->contain(['Pessoas', 'Objetos', 'Filiais', 'EntregaMeios']);
        $pedidos = $this->paginate($query);

        $requestQuery = $this->getRequest()->getQueryParams();
        $clientes = [];
        if (isset($requestQuery['cliente_id'])) {
            $clientes = $this->Pedidos->Pessoas
                ->listaClientes()
                ->where(['Pessoas.id' => $requestQuery['cliente_id']])
                ->toArray();
        }
        $cidades = [];
        if (isset($requestQuery['cidade_id'])) {
            $cidades = $this->Pedidos->Objetos->EnderecoColetas->Cidades
                ->listaCidades()
                ->where(function (QueryExpression $expression) use ($requestQuery) {
                    return $expression->eq('Cidades.id', $requestQuery['cidade_id']);
                })
                ->toArray();
        }

        $classificacoes = ObjetosTable::CLASSIFICACAO;
        $unidades_medidas_comprimento = ObjetosTable::UNIDADE_MEDIDA_COMPRIMENTO;
        $unidades_medidas_peso = ObjetosTable::UNIDADE_MEDIDA_PESO;
        $filiais = $this->Pedidos->Filiais->listaAtivas()->toArray();
        $entregaMeios = $this->Pedidos->EntregaMeios->listaAtivas()->toArray();
        $modalidadeDistribuicao = PedidosTable::MODALIDADE_DISTRIBUICAO;
        $isSearch = $this->Pedidos->isSearch();
        $this->set(compact(
            'pedidos',
            'clientes',
            'cidades',
            'classificacoes',
            'unidades_medidas_comprimento',
            'unidades_medidas_peso',
            'filiais',
            'entregaMeios',
            'modalidadeDistribuicao',
            'isSearch'
        ));
    }

    /**
     * aguardandoColetas method
     * Pedidos pagos e que a modalidade de distribuição é COLETA
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function aguardandoColetas()
    {
        $query = $this->Pedidos
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ])
            ->select([
                'Pedidos.id',
                'Pedidos.previsao_entrega',
                'Pedidos.created',
                'Pedidos.modified',
                'Pessoas.id',
                'Pessoas.nome',
                'tem_rota' => 'temRotaAtiva(Pedidos.id, \'' . RotaPedidosTable::COLETA . '\' )',
            ])
            ->contain([
                'Pessoas',
            ])
            ->where(function (QueryExpression $expression) {
                $expression
                    ->notIn('Pedidos.status', [
                        PedidosTable::PENDENTE,
                        PedidosTable::CANCELADO,
                        PedidosTable::FINALIZADO,
                    ])
                    ->eq('Pedidos.modalidade_distribuicao', PedidosTable::COLETA)
                    ->isNull('Pedidos.data_chegada') //objeto não coletado ou recebido
                    ->isNull('Pedidos.data_entrega'); //objeto não entregue

                return $expression;
            });

        $pedidos = $this->paginate($query);

        $requestQuery = $this->getRequest()->getQueryParams();
        $clientes = [];
        if (isset($requestQuery['cliente_id'])) {
            $clientes = $this->Pedidos->Pessoas
                ->listaClientes()
                ->where(['Pessoas.id' => $requestQuery['cliente_id']])
                ->toArray();
        }
        $cidades = [];
        if (isset($requestQuery['cidade_id'])) {
            $cidades = $this->Pedidos->Objetos->EnderecoColetas->Cidades
                ->listaCidades()
                ->where(function (QueryExpression $expression) use ($requestQuery) {
                    return $expression->eq('Cidades.id', $requestQuery['cidade_id']);
                })
                ->toArray();
        }

        $classificacoes = ObjetosTable::CLASSIFICACAO;
        $unidades_medidas_comprimento = ObjetosTable::UNIDADE_MEDIDA_COMPRIMENTO;
        $unidades_medidas_peso = ObjetosTable::UNIDADE_MEDIDA_PESO;
        $filiais = $this->Pedidos->Filiais->listaAtivas()->toArray();
        $entregaMeios = $this->Pedidos->EntregaMeios->listaAtivas()->toArray();
        $modalidadeDistribuicao = PedidosTable::MODALIDADE_DISTRIBUICAO;
        $isSearch = $this->Pedidos->isSearch();
        $this->set(compact(
            'pedidos',
            'clientes',
            'cidades',
            'classificacoes',
            'unidades_medidas_comprimento',
            'unidades_medidas_peso',
            'filiais',
            'entregaMeios',
            'modalidadeDistribuicao',
            'isSearch'
        ));
    }

    /**
     * aguardandoEntregas method
     * Pedidos pagos, coletados e que ainda não foram entregues
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function aguardandoEntregas()
    {
        $query = $this->Pedidos
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ])
            ->select([
                'Pedidos.id',
                'Pedidos.previsao_entrega',
                'Pedidos.created',
                'Pedidos.modified',
                'Pessoas.id',
                'Pessoas.nome',
                'tem_rota' => 'temRotaAtiva(Pedidos.id, \'' . RotaPedidosTable::ENTREGA . '\' )',
            ])
            ->contain([
                'Pessoas',
            ])
            ->where(function (QueryExpression $expression) {
                $expression
                    ->notIn('Pedidos.status', [
                        PedidosTable::PENDENTE,
                        PedidosTable::CANCELADO,
                        PedidosTable::FINALIZADO,
                    ])
                    ->isNotNull('Pedidos.data_chegada') //objeto não coletado ou recebido
                    ->isNull('Pedidos.data_entrega'); //objeto não entregue

                return $expression;
            });
        $pedidos = $this->paginate($query);

        $requestQuery = $this->getRequest()->getQueryParams();
        $clientes = [];
        if (isset($requestQuery['cliente_id'])) {
            $clientes = $this->Pedidos->Pessoas
                ->listaClientes()
                ->where(['Pessoas.id' => $requestQuery['cliente_id']])
                ->toArray();
        }
        $cidades = [];
        if (isset($requestQuery['cidade_id'])) {
            $cidades = $this->Pedidos->Objetos->EnderecoColetas->Cidades
                ->listaCidades()
                ->where(function (QueryExpression $expression) use ($requestQuery) {
                    return $expression->eq('Cidades.id', $requestQuery['cidade_id']);
                })
                ->toArray();
        }

        $classificacoes = ObjetosTable::CLASSIFICACAO;
        $unidades_medidas_comprimento = ObjetosTable::UNIDADE_MEDIDA_COMPRIMENTO;
        $unidades_medidas_peso = ObjetosTable::UNIDADE_MEDIDA_PESO;
        $filiais = $this->Pedidos->Filiais->listaAtivas()->toArray();
        $entregaMeios = $this->Pedidos->EntregaMeios->listaAtivas()->toArray();
        $modalidadeDistribuicao = PedidosTable::MODALIDADE_DISTRIBUICAO;
        $isSearch = $this->Pedidos->isSearch();
        $this->set(compact(
            'pedidos',
            'clientes',
            'cidades',
            'classificacoes',
            'unidades_medidas_comprimento',
            'unidades_medidas_peso',
            'filiais',
            'entregaMeios',
            'modalidadeDistribuicao',
            'isSearch'
        ));
    }

    /**
     * aguardandoFinalizarRotas method
     * Pedidos entregues em que a rota não foi finalizada
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function aguardandoFinalizarRotas()
    {
        $query = $this->Pedidos
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ])
            ->select([
                'Pedidos.id',
                'Pedidos.previsao_entrega',
                'Pessoas.id',
                'Pessoas.nome',
                'Rotas.status',
                'RotaPedidos.entregue',
            ])
            ->contain([
                'Pessoas',
                'RotaPedidos' => [
                    'joinType' => 'INNER',
                    'Rotas' => [
                        'joinType' => 'INNER',
                    ],
                ],
            ])
            ->innerJoinWith('RotaPedidos', function (Query $query) {
                return $query
                    ->orderDesc('data_saida')
                    ->innerJoinWith('Rotas');
            })
            ->where(function (QueryExpression $expression) {
                $expression
                    ->notEq('Rotas.status', RotasTable::FINALIZADA)
                    ->eq('RotaPedidos.entregue', true);

                return $expression;
            });
        $pedidos = $this->paginate($query);

        $isSearch = $this->Pedidos->isSearch();
        $this->set(compact('pedidos', 'isSearch'));
    }

    /**
     * View method
     *
     * @param string|null $id Pedido id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $pedido = $this->Pedidos->get($id, [
            'contain' => [
                'Pessoas',
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
                'EntregaMeios',
                'ColetaMeios',
                'Atualizacoes',
                'Pagamentos' => function (Query $query) {
                    return $query->orderDesc('Pagamentos.created');
                },
                'RotaPedidos' => function (Query $query) {
                    return $query
                        ->contain('Rotas')
                        ->orderDesc('RotaPedidos.created');
                },
            ],
        ]);

        $this->set(compact('pedido'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $pedido = $this->Pedidos->newEmptyEntity();
        $data = [];

        if ($this->getRequest()->is('post')) {
            $conn = $this->Pedidos->getConnection();
            try {
                $conn->begin();
                $data = $this->getRequest()->getData();

                if (isset($data['filial_id']) && !empty($data['filial_id']) && empty($data['prazo_envio'])) {
                    throw new \Exception('O prazo de envio não pode ser vazio.');
                }
                if (!isset($data['filial_id']) && isset($data['prazo_envio'])) {
                    unset($data['prazo_envio']);
                }
                if (
                    $data['modalidade_distribuicao'] === PedidosTable::ENTREGA
                    && isset($data['objeto']['endereco_coleta'])
                ) {
                    unset($data['objeto']['endereco_coleta']);
                }

                $data['status'] = PedidosTable::PENDENTE;
                $data['pagamentos'][0]['valor'] = $data['valor_total'];

                $pedido = $this->Pedidos->patchEntity($pedido, $data, [
                    'associated' => [
                        'Pagamentos',
                        'Objetos' => [
                            'associated' => [
                                'EnderecoColetas',
                                'EnderecoEntregas',
                            ],
                        ],
                    ],
                ]);

                $this->Pedidos->saveOrFail($pedido);

                $this->QueuedJobs->createJob('EmailNovoPedidoCliente', [
                    'pedido_id' => $pedido->id,
                ]);
                $this->QueuedJobs->createJob('EmailNovoPedidoAdmin', [
                    'pedido_id' => $pedido->id,
                ]);

                // ADICIONAR ATUALIZAÇÃO DO PEDIDO
                $atualizacao = [
                    'pedido_id' => $pedido->id,
                    'titulo' => 'Pedido realizado',
                    'descricao' => '',
                    'data' => new FrozenTime(),
                ];
                $this->Pedidos->Atualizacoes->add($atualizacao);

                $conn->commit();
                $this->Flash->success(__('O pedido foi salvo com sucesso.'));

                return $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('O pedido não pode ser salvo. Por favor, tente novamente.'));
            }
        }
        $clientes = [];
        if (isset($data['cliente_id'])) {
            $clientes = $this->Pedidos->Pessoas
                ->listaClientes()
                ->where(['Pessoas.id' => $data['cliente_id']])
                ->toArray();
        }
        $cidades_entregas = [];
        if (isset($data['objeto']['endereco_entrega']['cidade_id'])) {
            $cidades_entregas = $this->Pedidos->Objetos->EnderecoEntregas->Cidades
                ->listaCidades()
                ->where(function (QueryExpression $expression) use ($data) {
                    return $expression->eq('Cidades.id', $data['objeto']['endereco_entrega']['cidade_id']);
                })
                ->toArray();
        }
        $cidades_coletas = [];
        if (isset($data['objeto']['endereco_coleta']['cidade_id'])) {
            $cidades_coletas = $this->Pedidos->Objetos->EnderecoColetas->Cidades
                ->listaCidades()
                ->where(function (QueryExpression $expression) use ($data) {
                    return $expression->eq('Cidades.id', $data['objeto']['endereco_coleta']['cidade_id']);
                })
                ->toArray();
        }
        //------------Prazo de envio------------
        if (isset($data['prazo_envio'])) {
            $prazoEnvio = $data['prazo_envio'];
        } else {
            $dias = (int)$this->Configs->parametro('prazo_envio');
            $dataBase = new FrozenDate();
            $prazoEnvio = $dataBase->addDays($dias);
        }
        //------------End Prazo de envio------------
        $classificacoes = ObjetosTable::CLASSIFICACAO;
        $unidades_medidas_comprimento = ObjetosTable::UNIDADE_MEDIDA_COMPRIMENTO;
        $unidades_medidas_peso = ObjetosTable::UNIDADE_MEDIDA_PESO;
        $filiais = $this->Pedidos->Filiais->listaAtivas()->toArray();
        $entregaMeios = $this->Pedidos->EntregaMeios->listaAtivas()->toArray();
        $modalidadeDistribuicao = PedidosTable::MODALIDADE_DISTRIBUICAO;
        $status = PedidosTable::STATUS;
        $this->set(compact(
            'pedido',
            'clientes',
            'prazoEnvio',
            'status',
            'cidades_entregas',
            'cidades_coletas',
            'classificacoes',
            'filiais',
            'entregaMeios',
            'modalidadeDistribuicao',
            'unidades_medidas_comprimento',
            'unidades_medidas_peso'
        ));
    }

    /**
     * Edit method
     *
     * @param int|null $id Pedido id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     */
    public function edit($id = null)
    {
        $redirect = $this->getRequest()->getQuery('redirect');

        $pedido = $this->Pedidos->get($id, [
            'contain' => [
                'Pessoas',
                'Objetos' => [
                    'EnderecoEntregas' => [
                        'Cidades' => [
                            'Estados',
                        ],
                    ],
                    'EnderecoColetas' => [
                        'joinType' => 'LEFT',
                        'Cidades' => [
                            'joinType' => 'LEFT',
                            'Estados' => [
                                'joinType' => 'LEFT',
                            ],
                        ],
                    ],
                ],
                'Pagamentos' => function (Query $quer) {
                    return $quer->orderDesc('Pagamentos.created');
                },
            ],
        ]);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->Pedidos->getConnection();
            try {
                $conn->begin();
                $data = $this->getRequest()->getData();

                if (
                    $pedido->modalidade_distribuicao === PedidosTable::ENTREGA
                    && isset($data['objeto']['endereco_coleta'])
                ) {
                    unset($data['objeto']['endereco_coleta']);
                }

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

                $conn->commit();
                $this->Flash->success(__('O pedido foi salvo com sucesso.'));

                return $this->redirect(['action' => (empty($redirect) ? 'index' : $redirect)]);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('O pedido não pode ser salvo. Por favor, tente novamente.'));
            }
        }

        $clientes = [];
        if (isset($pedido->cliente_id)) {
            $clientes = $this->Pedidos->Pessoas
                ->listaClientes()
                ->where(['Pessoas.id' => $pedido->cliente_id])
                ->toArray();
        }
        $cidades_entregas = [];
        if (isset($pedido->objeto->endereco_entrega->id)) {
            $cidades_entregas = $this->Pedidos->Objetos->EnderecoEntregas->Cidades
                ->listaCidades()
                ->where(function (QueryExpression $expression) use ($pedido) {
                    return $expression->eq('Cidades.id', $pedido->objeto->endereco_entrega->cidade_id);
                })
                ->toArray();
        }
        $cidades_coletas = [];
        if (isset($pedido->objeto->endereco_coleta->id)) {
            $cidades_coletas = $this->Pedidos->Objetos->EnderecoColetas->Cidades
                ->listaCidades()
                ->where(function (QueryExpression $expression) use ($pedido) {
                    return $expression->eq('Cidades.id', $pedido->objeto->endereco_coleta->cidade_id);
                })
                ->toArray();
        }
        //------------Prazo de envio------------
        if (isset($pedido->prazo_envio)) {
            $prazoEnvio = $pedido->prazo_envio;
        } else {
            $dias = (int)$this->Configs->parametro('prazo_envio');
            $dataBase = new FrozenDate();
            $prazoEnvio = $dataBase->addDays($dias);
        }
        //------------End Prazo de envio------------
        $classificacoes = ObjetosTable::CLASSIFICACAO;
        $unidades_medidas_comprimento = ObjetosTable::UNIDADE_MEDIDA_COMPRIMENTO;
        $unidades_medidas_peso = ObjetosTable::UNIDADE_MEDIDA_PESO;
        $filiais = $this->Pedidos->Filiais->listaAll()->toArray();
        $entregaMeios = $this->Pedidos->EntregaMeios->listaAtivas()->toArray();
        $modalidadeDistribuicao = PedidosTable::MODALIDADE_DISTRIBUICAO;
        $status = PedidosTable::STATUS;
        $this->set(compact(
            'pedido',
            'clientes',
            'prazoEnvio',
            'cidades_entregas',
            'cidades_coletas',
            'classificacoes',
            'filiais',
            'entregaMeios',
            'modalidadeDistribuicao',
            'unidades_medidas_comprimento',
            'unidades_medidas_peso',
            'status',
            'redirect',
        ));
    }

    /**
     * addTratativa method
     * Registra dados de tratativa do pedido. Tratativa são pedidos que já ultrapassaram o limite de tentativas
     * de entrega e/ou coleta
     *
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     */
    public function addTratativa()
    {
        $data = $this->getRequest()->getData();
        $ids = explode('|', $data['ids']);
        unset($data['ids']);

        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->Pedidos->getConnection();
            try {
                $conn->begin();

                $this->Pedidos
                    ->find()
                    ->where(function (QueryExpression $expression) use ($ids) {
                        $expression->in('Pedidos.id', $ids);

                        return $expression;
                    })
                    ->each(function (Pedido $pedido) use ($data) {
                        $newData = [];
                        switch ($pedido->status) {
                            case PedidosTable::PROCESSO_COLETA:
                                $newData['data_chegada'] = new FrozenTime(
                                    str_replace('/', '-', $data['data_tratativa'])
                                );
                                $newData['data_tratativa_coleta'] = new FrozenTime(
                                    str_replace('/', '-', $data['data_tratativa'])
                                );
                                $newData['observacoes_tratativa_coleta'] = $data['observacoes_tratativa'];
                                $newData['status'] = PedidosTable::PROCESSO_ENTREGA;
                                break;
                            case PedidosTable::PROCESSO_ENTREGA:
                                $newData['data_entrega'] = new FrozenTime(
                                    str_replace('/', '-', $data['data_tratativa'])
                                );
                                $newData['data_tratativa_entrega'] = new FrozenTime(
                                    str_replace('/', '-', $data['data_tratativa'])
                                );
                                $newData['observacoes_tratativa_entrega'] = $data['observacoes_tratativa'];
                                $newData['status'] = PedidosTable::FINALIZADO;
                                break;
                            default:
                                break;
                        }

                        $this->Pedidos->patchEntity($pedido, $newData);
                        $this->Pedidos->saveOrFail($pedido);

                        $atualizacao = [
                            'pedido_id' => $pedido->id,
                            'titulo' => "Atualização do pedido #{$pedido->id}",
                            'descricao' => "Uma tratativa foi realizada para o pedido #{$pedido->id}",
                            'data' => new FrozenTime(),
                        ];

                        $this->Pedidos->Atualizacoes->add($atualizacao);
                    });

                $conn->commit();
                $this->Flash->success(__('Pedido salvo com sucesso.'));
            } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
                $conn->rollback();
                $this->log('Erro ao salvar tratativa: ' . $e->getMessage());
                $this->Flash->error(__($e->getMessage()));
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('Erro ao salvar tratativa. Por favor, tente novamente.'));
            }
        } else {
            throw new BadRequestException('Método inválido!');
        }

        return $this->redirect($this->referer());
    }

    /**
     * receberObjeto method
     * Registra data_chegada do objeto, seja pela modalidade de distribuição COLETA ou ENTREGA
     *
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     */
    public function receberObjetos()
    {
        $data = $this->getRequest()->getData();
        $ids = explode('|', $data['ids']);
        unset($data['ids']);

        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->Pedidos->getConnection();
            try {
                $conn->begin();

                $this->Pedidos
                    ->find()
                    ->contain([
                        'Objetos' => [
                            'EnderecoEntregas' => [
                                'Cidades' => [
                                    'Estados',
                                ],
                            ],
                            'EnderecoColetas' => [
                                'joinType' => 'LEFT',
                                'Cidades' => [
                                    'joinType' => 'LEFT',
                                    'Estados' => [
                                        'joinType' => 'LEFT',
                                    ],
                                ],
                            ],
                        ],
                    ])
                    ->where(function (QueryExpression $expression) use ($ids) {
                        $expression->in('Pedidos.id', $ids);

                        return $expression;
                    })
                    ->each(function (Pedido $pedido) use ($data) {
                        $data['status'] = PedidosTable::PROCESSO_ENTREGA;

                        $this->Pedidos->patchEntity($pedido, $data);
                        $this->Pedidos->saveOrFail($pedido);

                        // ADICIONAR ATUALIZAÇÃO DO PEDIDO
                        if ($pedido->modalidade_distribuicao === PedidosTable::ENTREGA) {
                            $titulo = 'Objeto recebido';
                            $descricao = 'Objeto recebido na filial: ' . $pedido->dados_filial;
                        } else {
                            $titulo = 'Objeto coletado';
                            $descricao = "Objeto coletado em: {$pedido->objeto->endereco_coleta->endereco_formatado}";

                            // Se tiver rota ativa marcar a parada como entregue
                            $this->Pedidos->RotaPedidos->marcarEntregue($pedido->id);
                        }

                        $atualizacao = [
                            'pedido_id' => $pedido->id,
                            'titulo' => $titulo,
                            'descricao' => $descricao,
                            'data' => new FrozenTime(),
                        ];

                        $this->Pedidos->Atualizacoes->add($atualizacao);
                    });

                $conn->commit();
                $this->Flash->success(__('Objeto(s) recebido(s) com sucesso.'));
            } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
                $conn->rollback();
                $this->log('Erro ao receber objeto: ' . $e->getMessage());
                $this->Flash->error(__($e->getMessage()));
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('Erro ao receber objeto. Por favor, tente novamente.'));
            }
        } else {
            throw new BadRequestException('Método inválido!');
        }

        return $this->redirect($this->referer());
    }

    /**
     * EntregarObjetos method
     * Registra data_entrega do objeto
     *
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     */
    public function entregarObjetos()
    {
        $data = $this->getRequest()->getData();
        $ids = explode('|', $data['ids']);
        unset($data['ids']);

        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->Pedidos->getConnection();
            try {
                $conn->begin();

                $this->Pedidos
                    ->find()
                    ->where(function (QueryExpression $expression) use ($ids) {
                        $expression->in('Pedidos.id', $ids);

                        return $expression;
                    })
                    ->each(function (Pedido $pedido) use ($data) {
                        $this->Pedidos->patchEntity($pedido, [
                            'data_entrega' => $data['data_entrega'],
                            'status' => PedidosTable::FINALIZADO,
                        ]);
                        $this->Pedidos->saveOrFail($pedido);

                        // Se tiver rota ativa marcar a parada como entregue
                        $this->Pedidos->RotaPedidos->marcarEntregue($pedido->id);

                        // ADICIONAR ATUALIZAÇÃO DO PEDIDO
                        $atualizacao = [
                            'pedido_id' => $pedido->id,
                            'titulo' => 'Objeto entregue',
                            'descricao' => "Objeto entregue em {$pedido->data_entrega}",
                            'data' => new FrozenTime(),
                        ];

                        $this->Pedidos->Atualizacoes->add($atualizacao);
                    });

                $conn->commit();
                $this->Flash->success(__('Objeto(s) entregue(s) com sucesso.'));
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('Erro ao entregar objeto. Por favor, tente novamente.'));
            }
        } else {
            throw new BadRequestException('Método inválido!');
        }

        return $this->redirect($this->referer());
    }

    /**
     * Delete method
     *
     * @param string|null $id Pedido id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->Pedidos->getConnection();
        try {
            $conn->begin();
            $pedido = $this->Pedidos->get($id, [
                'contain' => [
                    'Atualizacoes',
                    'RotaPedidos',
                ],
            ]);

            foreach ($pedido->atualizacoes as $atualizacao) {
                $this->Pedidos->Atualizacoes->deleteOrFail($atualizacao);
            }
            foreach ($pedido->rota_pedidos as $rotaPedido) {
                $this->Pedidos->RotaPedidos->deleteOrFail($rotaPedido);
            }

            $this->Pedidos->deleteOrFail($pedido);
            $conn->commit();
            $this->Flash->success(__('O pedido foi excluído com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('O pedido não pode ser excluído. Por favor, tente novamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * DeleteAll method
     *
     * @param string|null $ids Pedido id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteAll($ids = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->Pedidos->getConnection();
        try {
            $conn->begin();
            $this->Pedidos
                ->find()
                ->contain([
                    'Atualizacoes',
                    'RotaPedidos',
                ])
                ->where(function (QueryExpression $expression) use ($ids) {
                    $expression->in('Pedidos.id', explode('|', $ids));

                    return $expression;
                })
                ->each(function (Pedido $pedido) {
                    foreach ($pedido->atualizacoes as $atualizacao) {
                        $this->Pedidos->Atualizacoes->deleteOrFail($atualizacao);
                    }
                    foreach ($pedido->rota_pedidos as $rotaPedido) {
                        $this->Pedidos->RotaPedidos->deleteOrFail($rotaPedido);
                    }

                    $this->Pedidos->deleteOrFail($pedido);
                });
            $conn->commit();
            $this->Flash->success(__('O pedido foi excluído com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error($e->getMessage());
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * DefinirRota method
     *
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Http\Exception\BadRequestException
     */
    public function definirRota()
    {
        $data = $this->getRequest()->getData();
        $ids = explode('|', $data['ids-rotas']);
        unset($data['ids-rotas']);

        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->Pedidos->getConnection();
            try {
                $conn->begin();

                $this->Pedidos
                    ->find()
                    ->where(function (QueryExpression $expression) use ($ids) {
                        $expression->in('Pedidos.id', $ids);

                        return $expression;
                    })
                    ->each(function (Pedido $pedido) use ($data) {
                        // Verifica se o pedido já está em alguma rota
                        $rotaDefinida = $this->Pedidos->RotaPedidos->temRotaAtiva($pedido->id, $data['tipo']);
                        if ($rotaDefinida) {
                            throw new BadRequestException("Atenção! O pedido #{$pedido->id} já possui uma rota ativa.");
                        }

                        $rotaPedido = $this->Pedidos->RotaPedidos->newEntity([
                            'pedido_id' => $pedido->id,
                            'rota_id' => $data['rota_id'],
                            'tipo' => $data['tipo'],
                        ]);

                        $this->Pedidos->RotaPedidos->saveOrFail($rotaPedido);
                    });

                $conn->commit();
                $this->Flash->success(__('Rota definida com sucesso.'));
            } catch (BadRequestException $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error($e->getMessage());
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('Erro ao definir rota. Por favor, tente novamente.'));
            }
        } else {
            throw new BadRequestException('Método inválido!');
        }

        return $this->redirect($this->referer());
    }

    /**
     * All method
     * Retorna todos os pedidos com o id informado
     *
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Http\Exception\BadRequestException
     */
    public function all()
    {
        if ($this->getRequest()->is('ajax')) {
            $pedido_id = (int)$this->getRequest()->getQuery('pedido_id');

            $results = $this->Pedidos
                ->find('list', [
                    'keyField' => 'id',
                    'valueField' => function (Pedido $pedido) {
                        return sprintf('#%s - %s', $pedido->id, $pedido->pessoa->nome);
                    },
                ])
                ->contain([
                    'Pessoas',
                ])
                ->where(function (QueryExpression $expression) use ($pedido_id) {
                    $expression
                        ->eq('Pedidos.id', $pedido_id);

                    return $expression;
                })
                ->toArray();

            $this->set(compact('results'));
            $this->set('_serialize', ['results']);
        } else {
            throw new BadRequestException('Método inválido!');
        }
    }

    /**
     * classificar method
     * Retorna a classificação do objeto
     *
     * @return void
     */
    public function classificar()
    {
        $this->getRequest()->allowMethod('ajax');
        $params = $this->getRequest()->getQueryParams();

        $altura = $params['altura'];
        $largura = $params['largura'];
        $profundidade = $params['profundidade'];
        $unidade_medida_comprimento = $params['unidade_medida_comprimento'];

        $classificacao = $this->Configs->classificacao($altura, $largura, $profundidade, $unidade_medida_comprimento);

        $this->set(compact('classificacao'));
        $this->viewBuilder()->setOption('serialize', 'classificacao');
    }
}
