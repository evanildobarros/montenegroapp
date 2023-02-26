<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Entity\Atualizacao;
use App\Model\Entity\Pedido;
use Cake\Database\Expression\QueryExpression;

/**
 * Atualizacoes Controller
 *
 * @property \App\Model\Table\AtualizacoesTable $Atualizacoes
 * @property \Search\Controller\Component\SearchComponent $Search
 * @method \App\Model\Entity\Atualizacao[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AtualizacoesController extends AppController
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
     * @param int|null $pedido_id Pedido id
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index($pedido_id = null)
    {
        $pedido = $this->Atualizacoes->Pedidos->get($pedido_id);

        $query = $this->Atualizacoes
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ])
            ->contain(['Pedidos'])
            ->where(['pedido_id' => $pedido->id]);
        $atualizacoes = $this->paginate($query, [
            'order' => [
                'data' => 'DESC',
            ],
        ]);

        $isSearch = $this->Atualizacoes->isSearch();
        $this->set(compact('pedido', 'atualizacoes', 'isSearch'));
    }

    /**
     * Add method
     *
     * @param int|null $pedido_id Pedido id
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add($pedido_id = null)
    {
        $atualizacao = $this->Atualizacoes->newEmptyEntity();

        if ($this->getRequest()->is('post')) {
            $conn = $this->Atualizacoes->getConnection();
            try {
                $conn->begin();
                $data = $this->getRequest()->getData();

                $atualizacao = $this->Atualizacoes->patchEntity($atualizacao, $data);
                $this->Atualizacoes->saveOrFail($atualizacao);

                $conn->commit();
                $this->Flash->success(__('A atualização foi salva com sucesso.'));

                if (empty($pedido_id)) {
                    $pedido_id = $atualizacao->pedido_id;
                }

                return $this->redirect(['action' => 'index', $pedido_id]);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('A atualização não pode ser salva. Por favor, tente novamente.'));
            }
        }

        $pedido_selecionado = $pedido_id;
        if (isset($data['pedido_id'])) {
            $pedido = $this->Atualizacoes->Pedidos
                ->find('list', [
                    'keyField' => 'id',
                    'valueField' => function (Pedido $pedido) {
                        return sprintf('#%s - %s', $pedido->id, $pedido->pessoa->nome);
                    },
                ])
                ->contain('Pessoas')
                ->where(['Pedidos.id' => $data['pedido_id']])
                ->toArray();

            $pedido_selecionado = $data['pedido_id'];
        } else {
            if (empty($pedido_id)) {
                $pedido = [];
            } else {
                $pedido = $this->Atualizacoes->Pedidos
                    ->find('list', [
                        'keyField' => 'id',
                        'valueField' => function (Pedido $pedido) {
                            return sprintf('#%s - %s', $pedido->id, $pedido->pessoa->nome);
                        },
                    ])
                    ->contain('Pessoas')
                    ->where(['Pedidos.id' => $pedido_id])
                    ->toArray();
            }
        }

        $this->set(compact('atualizacao', 'pedido', 'pedido_id', 'pedido_selecionado'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Atualizacao id.
     * @param int|null $pedido_id Pedido id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null, $pedido_id = null)
    {
        $atualizacao = $this->Atualizacoes->get($id, [
            'contain' => [],
        ]);
        if (empty($pedido_id)) {
            $pedido_id = $atualizacao->pedido_id;
        }
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->Atualizacoes->getConnection();
            try {
                $conn->begin();
                $atualizacao = $this->Atualizacoes->patchEntity($atualizacao, $this->getRequest()->getData());
                $this->Atualizacoes->saveOrFail($atualizacao);

                $conn->commit();
                $this->Flash->success(__('A atualização foi salva com sucesso.'));

                return $this->redirect(['action' => 'index', $pedido_id]);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('A atualização não pode ser salva. Por favor, tente novamente.'));
            }
        }
        $pedidos = $this->Atualizacoes->Pedidos
            ->find('list', [
                'keyField' => 'id',
                'valueField' => function (Pedido $pedido) {
                    return sprintf('#%s - %s', $pedido->id, $pedido->pessoa->nome);
                },
            ])
            ->contain('Pessoas')
            ->where(['Pedidos.id' => $atualizacao->pedido_id]);
        $this->set(compact('atualizacao', 'pedido_id', 'pedidos'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Atualizacao id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->Atualizacoes->getConnection();
        try {
            $conn->begin();
            $atualizacao = $this->Atualizacoes->get($id);
            $this->Atualizacoes->deleteOrFail($atualizacao);
            $conn->commit();
            $this->Flash->success(__('A atualização foi excluída com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('A atualização não pode ser excluída. Por favor, tente novamente.'));
        }

        return $this->redirect($this->referer());
    }

    /**
     * DeleteAll method
     *
     * @param string|null $ids Atualizacao id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteAll($ids = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->Atualizacoes->getConnection();
        try {
            $conn->begin();
            $this->Atualizacoes
                ->find()
                ->where(function (QueryExpression $expression) use ($ids) {
                    $expression->in('Atualizacoes.id', explode('|', $ids));

                    return $expression;
                })
                ->each(function (Atualizacao $atualizacao) {
                    $this->Atualizacoes->deleteOrFail($atualizacao);
                });
            $conn->commit();
            $this->Flash->success(__('A atualização foi excluída com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('A atualização não pode ser excluída. Por favor, tente novamente.'));
        }

        return $this->redirect($this->referer());
    }
}
