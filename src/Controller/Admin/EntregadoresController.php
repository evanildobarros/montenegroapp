<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Entity\Pessoa;
use App\Model\Table\PessoasTable;
use Cake\Database\Expression\QueryExpression;
use Cake\I18n\FrozenDate;
use Cake\ORM\Query;

/**
 * Entregadores Controller
 *
 * @property \App\Model\Table\PessoasTable $Pessoas
 * @property \Search\Controller\Component\SearchComponent $Search
 * @method \App\Model\Entity\Pessoa[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EntregadoresController extends AppController
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

        $this->loadModel('Pessoas');
        $this->loadComponent('Search.Search', [
            'actions' => [
                'index',
                'entregas',
            ],
            'modelClass' => 'Pessoas',
        ]);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Pessoas
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ])
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
            ->where(function (QueryExpression $expression) {
                $expression
                    ->eq('Pessoas.model', PessoasTable::ENTREGADOR);

                return $expression;
            });
        $pessoas = $this->paginate($query);

        $cidade_id = $this->getRequest()->getQuery('cidade_id');
        $cidade = [];
        if (isset($cidade_id)) {
            $cidade = $this->Pessoas->Enderecos->Cidades
                ->listaCidades()
                ->where(function (QueryExpression $expression) use ($cidade_id) {
                    return $expression->eq('Cidades.id', $cidade_id);
                })
                ->toArray();
        }
        $tipos = PessoasTable::TIPOS;
        $status = PessoasTable::STATUS_CLIENTE;

        $isSearch = $this->Pessoas->isSearch();
        $this->set(compact('pessoas', 'tipos', 'status', 'cidade', 'isSearch'));
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function entregas()
    {
        $query = $this->Pessoas
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ])
            ->select([
                'Pessoas.id',
                'Pessoas.nome',
                'Pessoas.quantidade_entregas',
                'feitas' => 'SUM(CASE RotaPedidos.entregue WHEN 1 THEN 1 ELSE 0 END)',
                'total' => 'COUNT(RotaPedidos.entregue)',
                'tentativas' => 'COUNT(Tentativas.id)',
            ])
            ->innerJoinWith('Rotas', function (Query $query) {
                return $query
                    ->innerJoinWith('RotaPedidos', function (Query $query) {
                        return $query->leftJoinWith('Tentativas');
                    });
            })
            ->where(function (QueryExpression $expression) {
                $expression
                    ->eq('Pessoas.model', PessoasTable::ENTREGADOR);

                return $expression;
            })
            ->group([
                'Pessoas.id',
                'Pessoas.nome',
                'Pessoas.quantidade_entregas',
            ]);

        $data_inicio = $this->getRequest()->getQuery('data_inicio');
        $data_fim = $this->getRequest()->getQuery('data_fim');
        if (empty($data_inicio)) {
            $data_inicio = new FrozenDate();
        } else {
            $data_inicio = new FrozenDate(str_replace('/', '-', $data_inicio));
        }
        if (empty($data_fim)) {
            $data_fim = new FrozenDate();
        } else {
            $data_fim = new FrozenDate(str_replace('/', '-', $data_fim));
        }

        $query
            ->where(function (QueryExpression $expression) use ($data_inicio, $data_fim) {
                return $expression->between('Rotas.data_saida', $data_inicio, $data_fim);
            });

        $pessoas = $this->paginate($query);

        $isSearch = $this->Pessoas->isSearch();
        $this->set(compact('pessoas', 'data_inicio', 'data_fim', 'isSearch'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $pessoa = $this->Pessoas->newEmptyEntity();
        $data = [];

        if ($this->getRequest()->is('post')) {
            $conn = $this->Pessoas->getConnection();
            try {
                $conn->begin();
                $data = $this->getRequest()->getData();
                $pessoa = $this->Pessoas->patchEntity($pessoa, $data);
                $pessoa->model = PessoasTable::ENTREGADOR;
                $this->Pessoas->saveOrFail($pessoa);

                $conn->commit();
                $this->Flash->success(__('O entregador foi salvo com sucesso.'));

                return $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('O entregador não pode ser salvo. Por favor, tente novamente.'));
            }
        }

        $cidades = [];
        if (isset($data['endereco']['cidade_id'])) {
            $cidades = $this->Pessoas->Enderecos->Cidades
                ->listaCidades()
                ->where(function (QueryExpression $expression) use ($data) {
                    return $expression->eq('Cidades.id', $data['endereco']['cidade_id']);
                })
                ->toArray();
        }

        $quantidade_entregas = $data['quantidade_entregas'] ?? $this->Configs->parametro('quantidade_entregas');
        $tipo_selecionado = ($data['tipo'] ?? PessoasTable::FISICA);
        $tipos = PessoasTable::TIPOS;
        $status = PessoasTable::STATUS_ENTREGADOR;
        $this->set(compact('pessoa', 'tipos', 'status', 'cidades', 'tipo_selecionado', 'quantidade_entregas'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Pessoa id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $pessoa = $this->Pessoas->get($id, [
            'contain' => [
                'Enderecos' => [
                    'joinType' => 'LEFT',
                    'Cidades' => [
                        'joinType' => 'LEFT',
                        'Estados' => [
                            'joinType' => 'LEFT',
                        ],
                    ],
                ],
            ],
        ]);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->Pessoas->getConnection();
            try {
                $conn->begin();
                $pessoa = $this->Pessoas->patchEntity($pessoa, $this->getRequest()->getData());
                $this->Pessoas->saveOrFail($pessoa);

                $conn->commit();
                $this->Flash->success(__('O entregador foi salvo com sucesso.'));

                return $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('O entregador não pode ser salvo. Por favor, tente novamente.'));
            }
        }

        $cidade = [];
        if (!empty($pessoa->endereco->cidade_id)) {
            $cidade = $this->Pessoas->Enderecos->Cidades
                ->listaCidades()
                ->where(function (QueryExpression $expression) use ($pessoa) {
                    return $expression->eq('Cidades.id', $pessoa->endereco->cidade_id);
                })
                ->toArray();
        }

        $tipos = PessoasTable::TIPOS;
        $status = PessoasTable::STATUS_CLIENTE;
        $this->set(compact('pessoa', 'tipos', 'status', 'cidade'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Pessoa id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->Pessoas->getConnection();
        try {
            $conn->begin();
            $pessoa = $this->Pessoas->get($id);
            $this->Pessoas->deleteOrFail($pessoa);
            $conn->commit();
            $this->Flash->success(__('O entregador foi excluído com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('O entregador não pode ser excluído. Por favor, tente novamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * DeleteAll method
     *
     * @param string|null $ids Pessoa id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteAll($ids = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->Pessoas->getConnection();
        try {
            $conn->begin();
            $this->Pessoas
                ->find()
                ->where(function (QueryExpression $expression) use ($ids) {
                    $expression->in('Pessoas.id', explode('|', $ids));

                    return $expression;
                })
                ->each(function (Pessoa $pessoa) {
                    $this->Pessoas->deleteOrFail($pessoa);
                });
            $conn->commit();
            $this->Flash->success(__('O entregador foi excluído com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('O entregador não pode ser excluído. Por favor, tente novamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Ativos method
     *
     * @return void|null Return.
     */
    public function ativos()
    {
        $this->getRequest()->allowMethod('ajax');
        $param = $this->getRequest()->getQuery('nome');

        $pessoas = $this->Pessoas
            ->listaEntregadores()
            ->where(function (QueryExpression $expression) use ($param) {
                $expression
                    ->like('Pessoas.nome', '%' . $param . '%')
                    ->eq('Pessoas.status', PessoasTable::ATIVO);

                return $expression;
            });

        $this->set(compact('pessoas'));
        $this->viewBuilder()->setOption('serialize', ['results' => 'pessoas']);
    }

    /**
     * Ativos method
     *
     * @return void|null Return.
     */
    public function all()
    {
        $this->getRequest()->allowMethod('ajax');
        $param = $this->getRequest()->getQuery('nome');

        $pessoas = $this->Pessoas
            ->listaEntregadores()
            ->where(function (QueryExpression $expression) use ($param) {
                $expression
                    ->like('Pessoas.nome', '%' . $param . '%')
                    ->in('Pessoas.status', PessoasTable::STATUS_ENTREGADOR);

                return $expression;
            });

        $this->set('results', $pessoas);
        $this->set('_serialize', ['results']);
    }
}
