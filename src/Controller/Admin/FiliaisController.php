<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Entity\Filial;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Exception\BadRequestException;

/**
 * Filiais Controller
 *
 * @property \App\Model\Table\FiliaisTable $Filiais
 * @property \Search\Controller\Component\SearchComponent $Search
 * @method \App\Model\Entity\Filial[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class FiliaisController extends AppController
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
        $query = $this->Filiais
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ])
            ->contain([
                'Enderecos' => [
                    'Cidades' => [
                        'Estados',
                    ],
                ],
            ]);
        $filiais = $this->paginate($query);

        $cidade_id = $this->getRequest()->getQuery('cidade_id');
        $cidades = [];
        if (isset($cidade_id)) {
            $cidades =
                $this->Filiais->Enderecos->Cidades
                    ->listaCidades()
                    ->where(function (QueryExpression $expression) use ($cidade_id) {
                        return $expression->eq('Cidades.id', $cidade_id);
                    })
                    ->toArray();
        }

        $isSearch = $this->Filiais->isSearch();
        $this->set(compact('filiais', 'cidades', 'isSearch'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $filial = $this->Filiais->newEmptyEntity();
        $data = [];

        if ($this->getRequest()->is('post')) {
            $conn = $this->Filiais->getConnection();
            try {
                $conn->begin();
                $data = $this->getRequest()->getData();
                $filial = $this->Filiais->patchEntity($filial, $data);
                $this->Filiais->saveOrFail($filial);

                $conn->commit();
                $this->Flash->success(__('A filial foi salva com sucesso.'));

                return $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('A filial não pode ser salva. Por favor, tente novamente.'));
            }
        }
        $cidades = [];
        if (isset($data['cidade_id'])) {
            $cidades = $this->Filiais->Enderecos->Cidades
                ->listaCidades()
                ->where(function (QueryExpression $expression) use ($data) {
                    return $expression->eq('Cidades.id', $data['cidade_id']);
                })
                ->toArray();
        }
        $this->set(compact('filial', 'cidades'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Filial id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $filial = $this->Filiais->get($id, [
            'contain' => [
                'Enderecos' => [
                    'Cidades' => [
                        'Estados',
                    ],
                ],
            ],
        ]);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->Filiais->getConnection();
            try {
                $conn->begin();
                $filial = $this->Filiais->patchEntity($filial, $this->getRequest()->getData());
                $this->Filiais->saveOrFail($filial);

                $conn->commit();
                $this->Flash->success(__('A filial foi salva com sucesso.'));

                return $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('A filial não pode ser salva. Por favor, tente novamente.'));
            }
        }
        $cidades = [];
        if (!empty($filial->endereco->cidade_id)) {
            $cidades = $this->Filiais->Enderecos->Cidades
                ->listaCidades()
                ->where(function (QueryExpression $expression) use ($filial) {
                    return $expression->eq('Cidades.id', $filial->endereco->cidade_id);
                })
                ->toArray();
        }
        $this->set(compact('filial', 'cidades'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Filial id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->Filiais->getConnection();
        try {
            $conn->begin();
            $filial = $this->Filiais->get($id);
            $this->Filiais->deleteOrFail($filial);
            $conn->commit();
            $this->Flash->success(__('A filial foi excluída com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('A filial não pode ser excluída. Por favor, tente novamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * DeleteAll method
     *
     * @param string|null $ids Filial id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteAll($ids = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->Filiais->getConnection();
        try {
            $conn->begin();
            $this->Filiais
                ->find()
                ->where(function (QueryExpression $expression) use ($ids) {
                    $expression->in('Filiais.id', explode('|', $ids));

                    return $expression;
                })
                ->each(function (Filial $filial) {
                    $this->Filiais->deleteOrFail($filial);
                });
            $conn->commit();
            $this->Flash->success(__('A filial foi excluída com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('A filial não pode ser excluída. Por favor, tente novamente.'));
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

            $filial = $this->Filiais
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

            $filial = $this->Filiais->patchEntity($filial, [
                $campo => !$filial->get($campo),
            ]);

            $this->Filiais->saveOrFail($filial);

            $this->set(compact('filial'));
            $this->set('_serialize', ['filial']);
        } else {
            throw new BadRequestException('Método inválido!');
        }
    }
}
