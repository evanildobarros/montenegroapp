<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Entity\Zona;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Exception\BadRequestException;

/**
 * Zonas Controller
 *
 * @property \App\Model\Table\ZonasTable $Zonas
 * @property \Search\Controller\Component\SearchComponent $Search
 * @method \App\Model\Entity\Zona[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ZonasController extends AppController
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
        $queryParams = $this->getRequest()->getQueryParams();
        $query = $this->Zonas
            ->find('search', [
                'search' => $queryParams,
            ])
            ->contain([
                'Cidades' => [
                    'joinType' => 'LEFT',
                    'Estados' => [
                        'joinType' => 'LEFT',
                    ],
                ],
            ]);

        if (isset($queryParams['cep'])) {
            $query
                ->innerJoinWith('Faixas')
                ->group([
                    'Zonas.id',
                ]);
        }

        $bairros = $this->paginate($query);

        //----------------------------------------------------------------------------------------------

        /*
         * Quando der erro ao salvar, buscamos os dados da cidade selecionada
         * para que o select2 de cidades volte preenchido para o usuário
         */
        if (isset($queryParams['cidade_id'])) {
            $cidadeSelecionada = $this->Zonas->Cidades
                ->find()
                ->select([
                    'id',
                    'text' => "CONCAT( Cidades.nome, '/', Estados.sigla )",
                ])
                ->contain([
                    'Estados',
                ])
                ->where(function (QueryExpression $expression) use ($queryParams) {
                    return $expression->in('Cidades.id', $queryParams['cidade_id']);
                })
                ->first();
        } else {
            $cidadeSelecionada = [
                'id' => '',
                'text' => '',
            ];
        }
        //----------------------------------------------------------------------------------------------

        $isSearch = $this->Zonas->isSearch();
        $this->set(compact('bairros', 'isSearch', 'cidadeSelecionada'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $zona = $this->Zonas->newEmptyEntity();
        $data = [];
        if ($this->getRequest()->is('post')) {
            $conn = $this->Zonas->getConnection();
            try {
                $conn->begin();
                $data = $this->getRequest()->getData();

                $zona = $this->Zonas->patchEntity($zona, $data, [
                    'associated' => [
                        'Faixas',
                    ],
                ]);
                $this->Zonas->saveOrFail($zona);

                $conn->commit();
                $this->Flash->success(__('O bairro foi salvo com sucesso.'));

                return $this->redirect(['action' => 'index']);
            } catch (BadRequestException $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__($e->getMessage()));
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('O bairro não pode ser salvo. Por favor, tente novamente.'));
            }
        }

        //----------------------------------------------------------------------------------------------

        /*
         * Quando der erro ao salvar, buscamos os dados da cidade selecionada
         * para que o select2 de cidades volte preenchido para o usuário
         */
        if (isset($data['cidade_id'])) {
            $cidadeSelecionada = $this->Zonas->Cidades
                ->find()
                ->select([
                    'id',
                    'text' => "CONCAT( Cidades.nome, '/', Estados.sigla )",
                ])
                ->contain([
                    'Estados',
                ])
                ->where(function (QueryExpression $expression) use ($data) {
                    return $expression->in('Cidades.id', $data['cidade_id']);
                })
                ->first();
        } else {
            $cidadeSelecionada = [
                'id' => '',
                'text' => '',
            ];
        }
        //----------------------------------------------------------------------------------------------

        $this->set(compact('zona', 'cidadeSelecionada'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Zona id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $zona = $this->Zonas->get($id, [
            'contain' => [
                'Cidades' => [
                    'joinType' => 'LEFT',
                    'Estados' => [
                        'joinType' => 'LEFT',
                    ],
                ],
                'Faixas' => [
                    'joinType' => 'LEFT',
                ],
            ],
        ]);

        $cidadeSelecionada['id'] = $zona->cidade_id;
        $cidadeSelecionada['text'] = '';
        if (isset($zona->cidade->nome)) {
            $cidadeSelecionada['text'] = $zona->cidade->nome;
            if (isset($zona->cidade->estado->sigla)) {
                $cidadeSelecionada['text'] .= '/' . $zona->cidade->estado->sigla;
            }
        }

        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->Zonas->getConnection();
            try {
                $conn->begin();
                $data = $this->getRequest()->getData();

                /*
                 * Quando o usuário não informar nenhuma faixa, temos que adicionar o array vazio
                 * para que o CakePHP entenda que o usuário excluiu todas as faixas.
                 */
                if (!isset($data['faixas'])) {
                    $data['faixas'] = [];
                }

                $zona = $this->Zonas->patchEntity($zona, $data, [
                    'associated' => [
                        'Faixas',
                    ],
                ]);
                $this->Zonas->saveOrFail($zona);

                $conn->commit();
                $this->Flash->success(__('O bairro foi salvo com sucesso.'));

                return $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('O bairro não pode ser salvo. Por favor, tente novamente.'));
            }
        }

        //----------------------------------------------------------------------------------------------

        /*
         * Quando der erro ao salvar, buscamos os dados da cidade selecionada
         * para que o select2 de cidades volte preenchido para o usuário
         */
        if (isset($data['cidade_id'])) {
            $cidadeSelecionada = $this->Zonas->Cidades
                ->find()
                ->select([
                    'id',
                    'text' => "CONCAT( Cidades.nome, '/', Estados.sigla )",
                ])
                ->contain([
                    'Estados',
                ])
                ->where(function (QueryExpression $expression) use ($data) {
                    return $expression->in('Cidades.id', $data['cidade_id']);
                })
                ->first();
        } else {
            if (!isset($zona->cidade->nome)) {
                $cidadeSelecionada = [
                    'id' => '',
                    'text' => '',
                ];
            }
        }
        //----------------------------------------------------------------------------------------------

        $this->set(compact('zona', 'cidadeSelecionada'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Zona id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->Zonas->getConnection();
        try {
            $conn->begin();
            $zona = $this->Zonas->get($id);
            $this->Zonas->deleteOrFail($zona);
            $conn->commit();
            $this->Flash->success(__('O bairro foi excluído com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('O bairro não pode ser excluído. Por favor, tente novamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * DeleteAll method
     *
     * @param string|null $ids Zona id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteAll($ids = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->Zonas->getConnection();
        try {
            $conn->begin();
            $this->Zonas
                ->find()
                ->where(function (QueryExpression $expression) use ($ids) {
                    $expression->in('Zonas.id', explode('|', $ids));

                    return $expression;
                })
                ->each(function (Zona $zona) {
                    $this->Zonas->deleteOrFail($zona);
                });
            $conn->commit();
            $this->Flash->success(__('O bairro foi excluído com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('O bairro não pode ser excluído. Por favor, tente novamente.'));
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

            $zona = $this->Zonas
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

            $zona = $this->Zonas->patchEntity($zona, [
                $campo => !$zona->get($campo),
            ]);

            $this->Zonas->saveOrFail($zona);

            $this->set(compact('zona'));
            $this->set('_serialize', ['zona']);
        } else {
            throw new BadRequestException('Método inválido!');
        }
    }

    /**
     * bairrosPorCidade method
     * Busca os bairros conforme a cidade informada
     *
     * @return void
     */
    public function bairrosPorCidade()
    {
        $this->getRequest()->allowMethod('ajax');
        $cidade_id = $this->getRequest()->getQuery('cidade_id');
        $bairros = $this->Zonas
            ->find('list', [
                'keyField' => 'id',
                'valueField' => 'nome',
            ])
            ->where(function (QueryExpression $expression) use ($cidade_id) {
                $expression
                    ->eq('Zonas.cidade_id', $cidade_id);

                return $expression;
            })
            ->orderAsc('Zonas.nome');

        $this->set(compact('bairros'));
        $this->viewBuilder()->setOption('serialize', ['results' => 'bairros']);
    }
}
