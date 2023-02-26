<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Entity\Pedido;
use App\Model\Entity\RotaPedido;
use App\Model\Table\PedidosTable;
use App\Model\Table\RotaPedidosTable;
use App\Model\Table\RotasTable;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Exception\BadRequestException;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Query;

/**
 * RotaPedidos Controller
 *
 * @property \App\Model\Table\RotaPedidosTable $RotaPedidos
 * @property \Search\Controller\Component\SearchComponent $Search
 * @method \App\Model\Entity\RotaPedido[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RotaPedidosController extends AppController
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
     * @param int|null $rota_id Rota id.
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index($rota_id = null)
    {
        $rota = $this->RotaPedidos->Rotas->get($rota_id, [
            'contain' => [
                'Pessoas',
            ],
        ]);

        $query = $this->RotaPedidos
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ])
            ->contain([
                'Pedidos' => [
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
                ],
                'Tentativas' => [
                    'joinType' => 'LEFT',
                ],
            ])
            ->where(['rota_id' => $rota->id]);

        $rotaPedidos = $this->paginate($query, [
            'order' => [
                'ordem' => 'ASC',
            ],
        ]);

        $limite_tentativas = (int)$this->Configs->parametro('quantidade_tentativas');

        $isSearch = $this->RotaPedidos->isSearch();
        $this->set(compact('rota', 'rotaPedidos', 'limite_tentativas', 'isSearch'));
    }

    /**
     * mesmaRota method
     *
     * @param int|null $rota_id Rota id.
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function mesmaRota($rota_id = null)
    {
        $rota = $this->RotaPedidos->Rotas->get($rota_id);
        $rotaPedidos = $this->RotaPedidos
            ->find()
            ->contain([
                'Pedidos' => [
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
                ],
            ])
            ->where(function (QueryExpression $expression) use ($rota) {
                $expression
                    ->eq('RotaPedidos.tipo', RotaPedidosTable::COLETA)
                    ->eq('RotaPedidos.rota_id', $rota->id);

                return $expression;
            })
            ->toArray();

        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->RotaPedidos->getConnection();
            try {
                $conn->begin();
                $data = $this->getRequest()->getData();

                foreach ($data['rota_pedidos'] as $dadosRotaPedido) {
                    if ($dadosRotaPedido['mesma_rota']) {
                        $rotaPedido = $this->RotaPedidos->newEmptyEntity();
                        $rotaPedido = $this->RotaPedidos->patchEntity($rotaPedido, [
                            'parent_id' => $dadosRotaPedido['id'],
                            'rota_id' => $rota->id,
                            'pedido_id' => $dadosRotaPedido['pedido_id'],
                            'entregue' => false,
                            'tipo' => RotaPedidosTable::ENTREGA,
                        ]);

                        $this->RotaPedidos->saveOrFail($rotaPedido);
                    }
                }

                $conn->commit();
                $this->Flash->success(__('A parada foi salva com sucesso.'));

                return $this->redirect(['action' => 'ordenar', $rota->id]);
            } catch (PersistenceFailedException $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('Erro ao salvar parada: ' . $e->getMessage()));
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('A parada não pode ser salva. Por favor, tente novamente.'));
            }
        }

        $this->set(compact('rotaPedidos', 'rota'));
    }

    /**
     * Add method
     *
     * @param int|null $rota_id Rota id
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add($rota_id = null)
    {
        $rota = $this->RotaPedidos->Rotas->get($rota_id, [
            'contain' => [
                'Pessoas',
            ],
        ]);
        $quantidadeParadas = $this->RotaPedidos->Rotas->Pessoas->quantidadeParadas(
            $rota->entregador_id,
            $rota->data_saida
        );

        $rotaPedido = $this->RotaPedidos->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $conn = $this->RotaPedidos->getConnection();
            try {
                $conn->begin();
                $data = $this->getRequest()->getData();

                foreach ($data['rota_pedidos']['_ids'] as $rota_pedido_id) {
                    $pedido = $this->RotaPedidos->Pedidos->get($rota_pedido_id);

                    $rotaPedido = $this->RotaPedidos->newEmptyEntity();
                    $rotaPedido = $this->RotaPedidos->patchEntity($rotaPedido, [
                        'rota_id' => $rota->id,
                        'pedido_id' => $pedido->id,
                        'entregue' => false,
                        'tipo' => $pedido->etapa,
                    ]);

                    if ($rota->pessoa->quantidade_entregas <= $quantidadeParadas) {
                        throw new BadRequestException('Este entregador já atingiu o limite de entregas ' .
                            "diárias! Entregas ativas: {$quantidadeParadas}; Limite: " .
                            "{$rota->pessoa->quantidade_entregas}.");
                    }

                    $this->RotaPedidos->saveOrFail($rotaPedido);

                    $dataHoje = new FrozenDate();
                    if (
                        ($rota->data_saida->format('Y-m-d') === $dataHoje->format('Y-m-d'))
                        && $rota->status != RotasTable::EM_ROTA
                    ) {
                        $rota->status = RotasTable::EM_ROTA;

                        $this->RotaPedidos->Rotas->saveOrFail($rota);
                    }
                }

                $conn->commit();
                $this->Flash->success(__('A parada foi salva com sucesso.'));

                return $this->redirect(['action' => 'ordenar', $rota->id]);
            } catch (BadRequestException $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error($e->getMessage());
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('A parada não pode ser salva. Por favor, tente novamente.'));
            }
        }
        //Pedidos sem rota que foram selecionados
        $pedidos_sem_rotas_selecionados = [];
        if (isset($data['rota_pedidos']['_ids'])) {
            $pedidos_sem_rotas_selecionados = $this->RotaPedidos->Pedidos
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
                        'EnderecoColetas' => [
                            'Cidades' => [
                                'joinType' => 'LEFT',
                                'Estados' => [
                                    'joinType' => 'LEFT',
                                ],
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
        $pedidosSemRotaEntregas = $this->RotaPedidos->Pedidos->pedidosSemRotaEntregas();
        $pedidosSemRotaColetas = $this->RotaPedidos->Pedidos->pedidosSemRotaColetas();

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

        $this->set(compact(
            'rota',
            'rotaPedido',
            'pedidos_sem_rotas',
            'pedidos_sem_rotas_selecionados',
            'quantidadeParadas'
        ));
    }

    /**
     * Edit method
     *
     * @param string|null $id Rota Pedido id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $rotaPedido = $this->RotaPedidos->get($id, [
            'contain' => [
                'Pedidos',
                'ParentRotaPedidos',
            ],
        ]);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->RotaPedidos->getConnection();
            try {
                $conn->begin();
                $rotaPedido = $this->RotaPedidos->patchEntity($rotaPedido, $this->getRequest()->getData());

                //VERIFICAR SE EXISTE O MESMO PEDIDO EM OUTRA ROTA ATIVA
                $rota = $this->RotaPedidos->Rotas
                    ->rotasAtivas()
                    ->innerJoinWith('RotaPedidos')
                    ->where(function (QueryExpression $expression) use ($rotaPedido) {
                        $expression
                            ->notEq('RotaPedidos.id', $rotaPedido->id)
                            ->eq('RotaPedidos.pedido_id', $rotaPedido->pedido_id);

                        return $expression;
                    })
                    ->first();

                if (!empty($rotas)) {
                    throw new \Exception("Este pedido já está definido na rota #{$rota}");
                }

                $this->RotaPedidos->saveOrFail($rotaPedido);

                $conn->commit();
                $this->Flash->success(__('A parada foi salva com sucesso.'));

                return $this->redirect(['action' => 'index', $rotaPedido->rota_id]);
            } catch (PersistenceFailedException $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('Erro ao salvar parada: ' . $e->getMessage()));
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('A parada não pode ser salva. Por favor, tente novamente.'));
            }
        }
        $pedidos = $this->RotaPedidos->Pedidos->find('list')->where(['id' => $rotaPedido->pedido_id]);
        $rotas = $this->RotaPedidos->Rotas->rotasAtivas();
        $this->set(compact('rotaPedido', 'pedidos', 'rotas'));
    }

    /**
     * OrdenarParadas method
     *
     * @param string|null $rota_id Rota id.
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function ordenar($rota_id)
    {
        $rota = $this->RotaPedidos->Rotas->get($rota_id, [
            'contain' => [
                'RotaPedidos' => function (Query $query) {
                    return $query
                        ->contain([
                            'Pedidos' => [
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
                            ],
                        ])
                        ->orderAsc('RotaPedidos.ordem');
                },
            ],
        ]);

        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->RotaPedidos->Rotas->getConnection();
            try {
                $conn->begin();
                $data = $this->getRequest()->getData();

                $rota = $this->RotaPedidos->Rotas->patchEntity($rota, $data, [
                    'associated' => [
                        'RotaPedidos',
                    ],
                ]);
                $this->RotaPedidos->Rotas->saveOrFail($rota);

                $conn->commit();
                $this->Flash->success(__('A ordem foi salva com sucesso.'));

                return $this->redirect(['action' => 'index', $rota_id]);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('A ordem não pode ser salva. Por favor, tente novamente.'));
            }
        }

        $this->set(compact('rota'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Rota Pedido id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->RotaPedidos->getConnection();
        try {
            $conn->begin();
            $rotaPedido = $this->RotaPedidos->get($id);

            $rotasEntregas = $this->RotaPedidos->find()->where(['parent_id' => $id])->toArray();
            foreach ($rotasEntregas as $rotasEntrega) {
                $this->RotaPedidos->deleteOrFail($rotasEntrega);
            }

            $this->RotaPedidos->deleteOrFail($rotaPedido);
            $this->RotaPedidos->reordenar($rotaPedido->rota_id);
            $conn->commit();
            $this->Flash->success(__('A parada foi excluída com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('A parada não pode ser excluída. Por favor, tente novamente.'));
        }

        return $this->redirect($this->referer());
    }

    /**
     * DeleteAll method
     *
     * @param string|null $ids Rota Pedido id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteAll($ids = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->RotaPedidos->getConnection();
        try {
            $conn->begin();
            $this->RotaPedidos
                ->find()
                ->where(function (QueryExpression $expression) use ($ids) {
                    $expression->in('RotaPedidos.id', explode('|', $ids));

                    return $expression;
                })
                ->each(function (RotaPedido $rotaPedido) {
                    $rotasEntregas = $this->RotaPedidos->find()->where(['parent_id' => $rotaPedido->id])->toArray();
                    foreach ($rotasEntregas as $rotasEntrega) {
                        $this->RotaPedidos->deleteOrFail($rotasEntrega);
                    }

                    $this->RotaPedidos->deleteOrFail($rotaPedido);
                    $this->RotaPedidos->reordenar($rotaPedido->rota_id);
                });
            $conn->commit();
            $this->Flash->success(__('A parada foi excluída com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('A parada não pode ser excluída. Por favor, tente novamente.'));
        }

        return $this->redirect($this->referer());
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

            $rotaPedido = $this->RotaPedidos
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

            $rotaPedido = $this->RotaPedidos->patchEntity($rotaPedido, [
                $campo => !$rotaPedido->get($campo),
            ]);

            $this->RotaPedidos->saveOrFail($rotaPedido);

            $this->set(compact('rotaPedido'));
            $this->set('_serialize', ['rotaPedido']);
        } else {
            throw new BadRequestException('Método inválido!');
        }
    }

    /**
     * Entregar method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     */
    public function entregar()
    {
        $data = $this->getRequest()->getData();
        $ids = explode('|', $data['ids']);
        unset($data['ids']);

        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->RotaPedidos->getConnection();
            try {
                $conn->begin();

                $this->RotaPedidos
                    ->find()
                    ->where(function (QueryExpression $expression) use ($ids) {
                        $expression->in('RotaPedidos.id', $ids);

                        return $expression;
                    })
                    ->each(function (RotaPedido $rotaPedido) use ($data) {
                        if (!empty($rotaPedido->parent_id) && $rotaPedido->tipo === RotaPedidosTable::ENTREGA) {
                            $rotaColeta = $this->RotaPedidos->get($rotaPedido->parent_id);

                            if (!$rotaColeta->entregue) {
                                throw new BadRequestException("Atenção! Finalize a coleta #{$rotaColeta->id} "
                                    . "antes de finalizar a entrega #{$rotaPedido->id}");
                            }
                        }

                        $rotaPedido->entregue = true;
                        $this->RotaPedidos->saveOrFail($rotaPedido);

                        /** @var \App\Model\Entity\Pedido $pedido */
                        $pedido = $this->RotaPedidos->Pedidos->get($rotaPedido->pedido_id, [
                            'contain' => [
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
                            ],
                        ]);

                        $pedidoModificado = [];
                        if ($rotaPedido->tipo === RotaPedidosTable::ENTREGA) {
                            $titulo = 'Objeto entregue';
                            $descricao = 'Objeto entregue em: ' . $pedido->objeto->endereco_entrega->endereco_formatado;

                            $pedidoModificado['data_entrega'] = new FrozenTime(str_replace('/', '-', $data['data']));
                            $pedidoModificado['status'] = PedidosTable::FINALIZADO;
                        } else {
                            $titulo = 'Objeto coletado';
                            $descricao = 'Objeto coletado em: ' . $pedido->objeto->endereco_coleta->endereco_formatado;

                            $pedidoModificado['data_chegada'] = new FrozenTime(str_replace('/', '-', $data['data']));
                            $pedidoModificado['status'] = PedidosTable::PROCESSO_ENTREGA;
                        }
                        // Salvar pedidos
                        $pedidoModificado['nome_recebedor'] = $data['nome_recebedor'];
                        $pedidoModificado['documento_recebedor'] = $data['documento_recebedor'];
                        $pedidoModificado['comprovante'] = $data['comprovante'];
                        $pedido = $this->RotaPedidos->Pedidos->patchEntity($pedido, $pedidoModificado);
                        $this->RotaPedidos->Pedidos->saveOrFail($pedido);

                        // ADICIONAR ATUALIZAÇÃO DO PEDIDO
                        $atualizacao = [
                            'pedido_id' => $pedido->id,
                            'titulo' => $titulo,
                            'descricao' => $descricao,
                            'data' => new FrozenTime(),
                        ];

                        $this->RotaPedidos->Pedidos->Atualizacoes->add($atualizacao);
                    });

                $conn->commit();
                $this->Flash->success(__('A parada foi salva com sucesso.'));
            } catch (BadRequestException $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error($e->getMessage());
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('A parada não pode ser salva. Por favor, tente novamente.'));
            }
        } else {
            throw new BadRequestException('Método inválido!');
        }

        return $this->redirect($this->referer());
    }

    /**
     * All method
     * Retorna todos as rotas com o id informado
     *
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Http\Exception\BadRequestException
     */
    public function all()
    {
        if ($this->getRequest()->is('ajax')) {
            $id = (int)$this->getRequest()->getQuery('id');

            $results = $this->RotaPedidos
                ->find('list')
                ->where(function (QueryExpression $expression) use ($id) {
                    $expression
                        ->eq('RotaPedidos.id', $id);

                    return $expression;
                })
                ->toArray();

            $this->set(compact('results'));
            $this->set('_serialize', ['results']);
        } else {
            throw new BadRequestException('Método inválido!');
        }
    }
}
