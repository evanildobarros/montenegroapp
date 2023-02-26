<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Entity\Pessoa;
use App\Model\Table\PessoasTable;
use Cake\Database\Expression\QueryExpression;

/**
 * Clientes Controller
 *
 * @property \App\Model\Table\PessoasTable $Pessoas
 * @property \Search\Controller\Component\SearchComponent $Search
 * @method \App\Model\Entity\Pessoa[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ClientesController extends AppController
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
                    ->eq('Pessoas.model', PessoasTable::CLIENTE);

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
                $pessoa->model = PessoasTable::CLIENTE;
                $this->Pessoas->saveOrFail($pessoa);

                // Envia email de ativação de conta
                if ($pessoa->status === PessoasTable::AGUARDANDO_VALIDACAO) {
                    $this->QueuedJobs->createJob('EmailCadastro', [
                        'cliente_id' => $pessoa->id,
                    ]);
                }

                $conn->commit();
                $this->Flash->success(__('O cliente foi salvo com sucesso.'));

                return $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('O cliente não pode ser salvo. Por favor, tente novamente.'));
            }
        }

        $cidades = [];
        if (isset($data['endereco']['cidade_id'])) {
            $cidades = $this->Pessoas->Enderecos->Cidades
                ->listaCidades()
                ->where(function (QueryExpression $expression) use ($data) {
                    return $expression->eq('Cidades.id', $data['cidade_id']);
                })
                ->toArray();
        }

        $tipo_selecionado = ($data['tipo'] ?? PessoasTable::FISICA);
        $tipos = PessoasTable::TIPOS;
        $status = PessoasTable::STATUS_CLIENTE;
        $this->set(compact('pessoa', 'tipos', 'status', 'cidades', 'tipo_selecionado'));
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
                $this->Flash->success(__('O cliente foi salvo com sucesso.'));

                return $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('O cliente não pode ser salvo. Por favor, tente novamente.'));
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
            $this->Flash->success(__('O cliente foi excluído com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('O cliente não pode ser excluído. Por favor, tente novamente.'));
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
            $this->Flash->success(__('O cliente foi excluído com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('O cliente não pode ser excluído. Por favor, tente novamente.'));
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
            ->listaClientes()
            ->where(function (QueryExpression $expression) use ($param) {
                $expression
                    ->like('Pessoas.nome', '%' . $param . '%')
                    ->eq('Pessoas.status', PessoasTable::ATIVO);

                return $expression;
            });

        $this->set('results', $pessoas);
        $this->set('_serialize', ['results']);
    }

    /**
     * All method
     *
     * @return void|null Return.
     */
    public function all()
    {
        $this->getRequest()->allowMethod('ajax');
        $param = $this->getRequest()->getQuery('nome');

        $pessoas = $this->Pessoas
            ->listaClientes()
            ->where(function (QueryExpression $expression) use ($param) {
                $expression
                    ->like('Pessoas.nome', '%' . $param . '%');

                return $expression;
            });

        $this->set('results', $pessoas);
        $this->set('_serialize', ['results']);
    }

    /**
     * reenviar Method
     * Reenvia o email de ativação para o cliente
     *
     * @param int $id ID da pessoa
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function reenviar($id)
    {
        
        try {
            $cliente = $this->Pessoas->get($id);

            if ($cliente->status === PessoasTable::AGUARDANDO_VALIDACAO) {
                $this->QueuedJobs->createJob('EmailCadastro', [
                    'cliente_id' => $cliente->id,
                ]);

                $this->Flash->success(__('Email enviado com sucesso.'));
            } else {
                throw new \Exception('Atenção! Usuário já está ativo!');
            }
        } catch (\Exception $e) {
            $this->log($e->getMessage());
            $this->Flash->error(__('O email não pode ser enviado. Por favor, tente novamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
