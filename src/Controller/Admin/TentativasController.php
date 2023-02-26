<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Entity\Tentativa;
use Cake\Database\Expression\QueryExpression;
use Cake\I18n\FrozenTime;

/**
 * Tentativas Controller
 *
 * @property \App\Model\Table\TentativasTable $Tentativas
 * @property \Search\Controller\Component\SearchComponent $Search
 * @method \App\Model\Entity\Tentativa[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TentativasController extends AppController
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
     * @param int|null $rota_pedido_id RotaPedidos id
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index($rota_pedido_id = null)
    {
        $rota_pedido = $this->Tentativas->RotaPedidos->get($rota_pedido_id, [
            'contain' => [
                'Rotas' => [
                    'Pessoas',
                ],
            ],
        ]);

        $query = $this->Tentativas
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ])
            ->contain(['RotaPedidos', 'Motivos'])
            ->where(['Tentativas.rota_pedido_id' => $rota_pedido->id]);

        $tentativas = $this->paginate($query);

        $isSearch = $this->Tentativas->isSearch();
        $this->set(compact('tentativas', 'isSearch', 'rota_pedido'));
    }

    /**
     * View method
     *
     * @param string|null $id Tentativa id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $tentativa = $this->Tentativas->get($id, [
            'contain' => ['RotaPedidos', 'Motivos'],
        ]);

        $this->set(compact('tentativa'));
    }

    /**
     * Add method
     *
     * @param int|null $rota_pedido_id RotaPedidos id
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add($rota_pedido_id = null)
    {
        $rotaPedido = $this->Tentativas->RotaPedidos->get($rota_pedido_id);
        $tentativa = $this->Tentativas->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $conn = $this->Tentativas->getConnection();
            try {
                $conn->begin();
                $data = $this->getRequest()->getData();
                $data['rota_pedido_id'] = $rotaPedido->id;
                $tentativa = $this->Tentativas->patchEntity($tentativa, $data);
                $this->Tentativas->saveOrFail($tentativa);

                // ADICIONAR ATUALIZAÇÃO DO PEDIDO
                $rotaPedido = $this->Tentativas->RotaPedidos->get($tentativa->rota_pedido_id);
                $atualizacao = [
                    'pedido_id' => $rotaPedido->pedido_id,
                    'titulo' => "Tentativa de {$rotaPedido->tipo_formatado}",
                    'descricao' => "Tentativa de entrega no dia {$tentativa->data}; " .
                        "/n Motivo: {$tentativa->nome_motivo}; /n Observações: {$tentativa->observacoes}",
                    'data' => new FrozenTime(),
                ];
                $this->Tentativas->RotaPedidos->Pedidos->Atualizacoes->add($atualizacao);

                $conn->commit();
                $this->Flash->success(__('A tentativa foi salva com sucesso.'));

                if (empty($rota_pedido_id)) {
                    $rota_pedido_id = $tentativa->rota_pedido_id;
                }

                return $this->redirect(['action' => 'index', $rota_pedido_id]);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('A tentativa não pode ser salva. Por favor, tente novamente.'));
            }
        }

        $parada_selecionada = $rota_pedido_id;
        if (isset($data['rota_pedido_id'])) {
            $rotaPedidos = $this->Tentativas->RotaPedidos
                ->find('list')
                ->where(['RotaPedidos.id' => $data['rota_pedido_id']])
                ->toArray();

            $parada_selecionada = $data['rota_pedido_id'];
        } else {
            if (empty($rota_pedido_id)) {
                $rotaPedidos = [];
            } else {
                $rotaPedidos = $this->Tentativas->RotaPedidos
                    ->find('list')
                    ->where(['RotaPedidos.id' => $rota_pedido_id])
                    ->toArray();
            }
        }

        $motivos = $this->Tentativas->Motivos->motivosAtivos();
        $this->set(compact('tentativa', 'rotaPedidos', 'motivos', 'rota_pedido_id', 'parada_selecionada'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Tentativa id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $tentativa = $this->Tentativas->get($id, [
            'contain' => [],
        ]);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->Tentativas->getConnection();
            try {
                $conn->begin();
                $data = $this->getRequest()->getData();
                $tentativa = $this->Tentativas->patchEntity($tentativa, $data);
                $this->Tentativas->saveOrFail($tentativa);

                $conn->commit();
                $this->Flash->success(__('A tentativa foi salva com sucesso.'));

                return $this->redirect(['action' => 'index', $tentativa->rota_pedido_id]);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('A tentativa não pode ser salva. Por favor, tente novamente.'));
            }
        }

        if (isset($data['rota_pedido_id'])) {
            $rotaPedidos = $this->Tentativas->RotaPedidos
                ->find('list')
                ->where(['RotaPedidos.id' => $data['rota_pedido_id']])
                ->toArray();
        } else {
            $rotaPedidos = $this->Tentativas->RotaPedidos
                ->find('list')
                ->where(['RotaPedidos.id' => $tentativa->rota_pedido_id])
                ->toArray();
        }

        $motivo_selecionado = $this->Tentativas->Motivos->find()->where(['id' => $tentativa->motivo_id])->first()->id;
        $motivos = $this->Tentativas->Motivos->motivosAtivos();
        $this->set(compact('tentativa', 'rotaPedidos', 'motivos', 'motivo_selecionado'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Tentativa id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->Tentativas->getConnection();
        try {
            $conn->begin();
            $tentativa = $this->Tentativas->get($id);
            $this->Tentativas->deleteOrFail($tentativa);
            $conn->commit();
            $this->Flash->success(__('A tentativa foi excluída com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('A tentativa não pode ser excluída. Por favor, tente novamente.'));
        }

        return $this->redirect($this->referer());
    }

    /**
     * DeleteAll method
     *
     * @param string|null $ids Tentativa id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteAll($ids = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->Tentativas->getConnection();
        try {
            $conn->begin();
            $this->Tentativas
                ->find()
                ->where(function (QueryExpression $expression) use ($ids) {
                    $expression->in('Tentativas.id', explode('|', $ids));

                    return $expression;
                })
                ->each(function (Tentativa $tentativa) {
                    $this->Tentativas->deleteOrFail($tentativa);
                });
            $conn->commit();
            $this->Flash->success(__('A tentativa foi excluída com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('A tentativa não pode ser excluída. Por favor, tente novamente.'));
        }

        return $this->redirect($this->referer());
    }
}
