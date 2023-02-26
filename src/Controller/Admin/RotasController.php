<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Entity\Pedido;
use App\Model\Entity\Rota;
use App\Model\Table\PedidosTable;
use App\Model\Table\RotaPedidosTable;
use App\Model\Table\RotasTable;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Exception\BadRequestException;
use Cake\I18n\Date;
use Cake\I18n\FrozenDate;

/**
 * Rotas Controller
 *
 * @property \App\Model\Table\RotasTable $Rotas
 * @property \Search\Controller\Component\SearchComponent $Search
 * @method \App\Model\Entity\Rota[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RotasController extends AppController
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
        $requestQuery = $this->getRequest()->getQueryParams();
        $query = $this->Rotas
            ->find('search', [
                'search' => $requestQuery,
            ])
            ->contain(['Pessoas', 'RotaPedidos']);
        $rotas = $this->paginate($query);

        $pessoas = [];
        if (isset($requestQuery['entregador_id'])) {
            $pessoas = $this->Rotas->Pessoas
                ->listaClientes()
                ->where(['Pessoas.id' => $requestQuery['entregador_id']])
                ->toArray();
        }
        $isSearch = $this->Rotas->isSearch();
        $this->set(compact('rotas', 'pessoas', 'isSearch'));
    }

    /**
     * View method
     *
     * @param string|null $id Rota id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $rota = $this->Rotas->get($id, [
            'contain' => ['Pessoas', 'RotaPedidos'],
        ]);

        $this->set(compact('rota'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $rota = $this->Rotas->newEmptyEntity();
        $data = [];

        if ($this->getRequest()->is('post')) {
            $conn = $this->Rotas->getConnection();
            try {
                $conn->begin();
                $data = $this->getRequest()->getData();

                $data_saida = new Date(str_replace('/', '-', $data['data_saida']));
                $quantidadeParadas = $this->Rotas->Pessoas->quantidadeParadas($data['entregador_id'], $data_saida);
                $entregador = $this->Rotas->Pessoas->get($data['entregador_id']);

                if (
                    !empty($entregador->quantidade_entregas) &&
                    $entregador->quantidade_entregas <= $quantidadeParadas
                ) {
                    throw new BadRequestException('Este entregador já atingiu o limite de entregas ' .
                        "diárias! Entregas ativas: {$quantidadeParadas}; Limite: " .
                        "{$rota->pessoa->quantidade_entregas}.");
                }

                foreach ($data['rota_pedidos']['_ids'] as $rota_pedido_id) {
                    $pedido = $this->Rotas->RotaPedidos->Pedidos->get($rota_pedido_id);

                    $data['rota_pedidos'][] = [
                        'pedido_id' => $pedido->id,
                        'entregue' => false,
                        'tipo' => $pedido->etapa,
                    ];
                }
                unset($data['rota_pedidos']['_ids']);
                $rota = $this->Rotas->patchEntity($rota, $data, [
                    'associated' => [
                        'RotaPedidos',
                    ],
                ]);
                $this->Rotas->saveOrFail($rota);

                $conn->commit();
                $this->Flash->success(__('A rota foi salva com sucesso.'));

                $rotaPedidoColeta = $this->Rotas->RotaPedidos
                    ->find()
                    ->where([
                        'rota_id' => $rota->id,
                        'tipo' => RotaPedidosTable::COLETA,
                    ])
                    ->count();

                if ($rotaPedidoColeta > 0) {
                    return $this->redirect(['controller' => 'RotaPedidos', 'action' => 'mesmaRota', $rota->id]);
                }

                return $this->redirect(['controller' => 'RotaPedidos', 'action' => 'ordenar', $rota->id]);
            } catch (BadRequestException $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error($e->getMessage());
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('A rota não pode ser salva. Por favor, tente novamente.'));
            }
        }
        $pessoas = [];
        if (isset($data['entregador_id'])) {
            $pessoas = $this->Rotas->Pessoas
                ->listaEntregadores()
                ->where(['Pessoas.id' => $data['entregador_id']])
                ->toArray();
        }

        //Pedidos sem rota que foram selecionados
        $pedidos_sem_rotas_selecionados = [];
        if (isset($data['rota_pedidos']['_ids'])) {
            $pedidos_sem_rotas_selecionados = $this->Rotas->RotaPedidos->Pedidos
                ->find('list', [
                    'keyField' => 'id',
                    'valueField' => function (Pedido $entity) {
                        if ($entity->etapa === PedidosTable::COLETA) {
                            $endereco = $entity->objeto->endereco_coleta->endereco_formatado;
                        } else {
                            $endereco = $entity->objeto->endereco_entrega->endereco_formatado;
                        }

                        return sprintf(
                            '#%s [%s] - %s',
                            $entity->id,
                            PedidosTable::MODALIDADE_DISTRIBUICAO[$entity->etapa],
                            $endereco,
                        );
                    },
                ])
                ->contain([
                    'Objetos' => [
                        'EnderecoEntregas' => [
                            'Cidades' => [
                                'Estados',
                            ],
                        ],
                    ],
                ])
                ->where(function (QueryExpression $expression) use ($data) {
                    return $expression->in('Pedidos.id', $data['rota_pedidos']['_ids']);
                })
                ->toArray();
        }

        //Pedidos sem rotas
        $pedidosSemRotaEntregas = $this->Rotas->RotaPedidos->Pedidos->pedidosSemRotaEntregas();
        $pedidosSemRotaColetas = $this->Rotas->RotaPedidos->Pedidos->pedidosSemRotaColetas();

        if (!empty($pedidos_sem_rotas_selecionados)) {
            $pedidosSemRotaEntregas->where(
                function (QueryExpression $expression) use ($pedidos_sem_rotas_selecionados) {
                    return $expression->notIn('Pedidos.id', array_keys($pedidos_sem_rotas_selecionados));
                }
            );
            $pedidosSemRotaColetas->where(
                function (QueryExpression $expression) use ($pedidos_sem_rotas_selecionados) {
                    return $expression->notIn('Pedidos.id', array_keys($pedidos_sem_rotas_selecionados));
                }
            );
        }

        $pedidos_sem_rotas = $pedidosSemRotaEntregas->union($pedidosSemRotaColetas)->toArray();

        $status = RotasTable::STATUS;
        $this->set(compact(
            'rota',
            'status',
            'pessoas',
            'pedidos_sem_rotas',
            'pedidos_sem_rotas_selecionados'
        ));
    }

    /**
     * Edit method
     *
     * @param string|null $id Rota id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $rota = $this->Rotas->get($id, [
            'contain' => [
                'RotaPedidos',
            ],
        ]);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->Rotas->getConnection();
            try {
                $conn->begin();

                $rota = $this->Rotas->patchEntity($rota, $this->getRequest()->getData(), [
                    'associated' => [
                        'RotaPedidos',
                    ],
                ]);
                $this->Rotas->saveOrFail($rota);

                $conn->commit();
                $this->Flash->success(__('A rota foi salva com sucesso.'));

                return $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('A rota não pode ser salva. Por favor, tente novamente.'));
            }
        }

        $pessoas = $this->Rotas->Pessoas
            ->listaEntregadores()
            ->where(['Pessoas.id' => $rota->entregador_id])
            ->toArray();

        $status = RotasTable::STATUS;
        $this->set(compact('rota', 'status', 'pessoas'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Rota id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->Rotas->getConnection();
        try {
            $conn->begin();
            $rota = $this->Rotas->get($id, [
                'contain' => [
                    'RotaPedidos',
                ],
            ]);

            foreach ($rota->rota_pedidos as $rota_pedido) {
                $this->Rotas->RotaPedidos->deleteOrFail($rota_pedido);
            }

            $this->Rotas->deleteOrFail($rota);
            $conn->commit();
            $this->Flash->success(__('A rota foi excluído com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('A rota não pode ser excluído. Por favor, tente novamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * DeleteAll method
     *
     * @param string|null $ids Rota id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteAll($ids = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->Rotas->getConnection();
        try {
            $conn->begin();
            $this->Rotas
                ->find()
                ->where(function (QueryExpression $expression) use ($ids) {
                    $expression->in('Rotas.id', explode('|', $ids));

                    return $expression;
                })
                ->each(function (Rota $rota) {
                    $this->Rotas->deleteOrFail($rota);
                });
            $conn->commit();
            $this->Flash->success(__('A rota foi excluído com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('A rota não pode ser excluído. Por favor, tente novamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * getPedidosSemRotas method
     * Retorna os pedidos que não possuem rotas definidas
     *
     * @return void|null
     * @throws \Cake\Http\Exception\BadRequestException
     */
    public function getPedidosSemRotas()
    {
        if ($this->getRequest()->is('ajax')) {
            $data = $this->getRequest()->getQueryParams();

            //Pedidos sem rotas
            $pedidosSemRotaEntregas = $this->Rotas->RotaPedidos->Pedidos->pedidosSemRotaEntregas();
            $pedidosSemRotaColetas = $this->Rotas->RotaPedidos->Pedidos->pedidosSemRotaColetas();

            $pedidosSemRotaEntregas
                ->where(function (QueryExpression $expression) use ($data) {
                    $or = $expression->or(function (QueryExpression $orExpression) {
                        return $orExpression
                            ->gte('RotaPedidos.entregue', false)
                            ->isNull('RotaPedidos.id');
                    });
                    $expression->add($or);

                    if (!empty($data['codigo'])) {
                        $expression
                            ->eq('Pedidos.id', $data['codigo']);
                    }

                    if (!empty($data['data_pedido'])) {
                        $dataPedido = new FrozenDate(str_replace('/', '-', $data['data_pedido']));
                        $dataPedido = $dataPedido->format('Y-m-d');
                        $expression
                            ->eq('DATE_FORMAT(Pedidos.created, \'%Y-%m-%d\')', $dataPedido);
                    }

                    if (!empty($data['previsao_entrega'])) {
                        $previsaoEntrega = new FrozenDate(str_replace('/', '-', $data['previsao_entrega']));
                        $previsaoEntrega = $previsaoEntrega->format('Y-m-d');
                        $expression
                            ->eq('Pedidos.previsao_entrega', $previsaoEntrega);
                    }

                    return $expression;
                });
            $pedidosSemRotaColetas
                ->where(function (QueryExpression $expression) use ($data) {
                    $or = $expression->or(function (QueryExpression $orExpression) {
                        return $orExpression
                            ->gte('RotaPedidos.entregue', false)
                            ->isNull('RotaPedidos.id');
                    });
                    $expression->add($or);

                    if (!empty($data['codigo'])) {
                        $expression
                            ->eq('Pedidos.id', $data['codigo']);
                    }

                    if (!empty($data['data_pedido'])) {
                        $dataPedido = new FrozenDate(str_replace('/', '-', $data['data_pedido']));
                        $dataPedido = $dataPedido->format('Y-m-d');
                        $expression
                            ->eq('DATE_FORMAT(Pedidos.created, \'%Y-%m-%d\')', $dataPedido);
                    }

                    if (!empty($data['previsao_entrega'])) {
                        $previsaoEntrega = new FrozenDate(str_replace('/', '-', $data['previsao_entrega']));
                        $previsaoEntrega = $previsaoEntrega->format('Y-m-d');
                        $expression
                            ->eq('Pedidos.previsao_entrega', $previsaoEntrega);
                    }

                    return $expression;
                });

            $pedidos = $pedidosSemRotaEntregas->union($pedidosSemRotaColetas)->toArray();

            $this->set(compact('pedidos'));
            $this->viewBuilder()->setOption('serialize', ['pedidos']);
        } else {
            throw new BadRequestException('Método inválido!');
        }
    }

    /**
     * Ativas method
     *
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Http\Exception\BadRequestException
     */
    public function ativas()
    {
        if ($this->getRequest()->is('ajax')) {
            $results = $this->Rotas->rotasAtivas()->toArray();

            $this->set(compact('results'));
            $this->viewBuilder()->setOption('serialize', ['results']);
        } else {
            throw new BadRequestException('Método inválido!');
        }
    }
}
