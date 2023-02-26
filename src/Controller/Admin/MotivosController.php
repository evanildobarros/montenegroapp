<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Entity\Motivo;
use Cake\Database\Expression\QueryExpression;

/**
 * Motivos Controller
 *
 * @property \App\Model\Table\MotivosTable $Motivos
 * @property \Search\Controller\Component\SearchComponent $Search
 * @method \App\Model\Entity\Motivo[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MotivosController extends AppController
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
        $query = $this->Motivos
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ]);
        $motivos = $this->paginate($query);

        $isSearch = $this->Motivos->isSearch();
        $this->set(compact('motivos', 'isSearch'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $motivo = $this->Motivos->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $conn = $this->Motivos->getConnection();
            try {
                $conn->begin();
                $motivo = $this->Motivos->patchEntity($motivo, $this->getRequest()->getData());
                $this->Motivos->saveOrFail($motivo);

                $conn->commit();
                $this->Flash->success(__('O motivo foi salvo com sucesso.'));

                return $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('O motivo não pode ser salvo. Por favor, tente novamente.'));
            }
        }
        $this->set(compact('motivo'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Motivo id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $motivo = $this->Motivos->get($id, [
            'contain' => [],
        ]);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->Motivos->getConnection();
            try {
                $conn->begin();
                $motivo = $this->Motivos->patchEntity($motivo, $this->getRequest()->getData());
                $this->Motivos->saveOrFail($motivo);

                $conn->commit();
                $this->Flash->success(__('O motivo foi salvo com sucesso.'));

                return $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->log($e->getMessage());
                $this->Flash->error(__('O motivo não pode ser salvo. Por favor, tente novamente.'));
            }
        }
        $this->set(compact('motivo'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Motivo id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->Motivos->getConnection();
        try {
            $conn->begin();
            $motivo = $this->Motivos->get($id);
            $this->Motivos->deleteOrFail($motivo);
            $conn->commit();
            $this->Flash->success(__('O motivo foi excluído com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('O motivo não pode ser excluído. Por favor, tente novamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * DeleteAll method
     *
     * @param string|null $ids Motivo id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteAll($ids = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->Motivos->getConnection();
        try {
            $conn->begin();
            $this->Motivos
                ->find()
                ->where(function (QueryExpression $expression) use ($ids) {
                    $expression->in('Motivos.id', explode('|', $ids));

                    return $expression;
                })
                ->each(function (Motivo $motivo) {
                    $this->Motivos->deleteOrFail($motivo);
                });
            $conn->commit();
            $this->Flash->success(__('O motivo foi excluído com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            $this->Flash->error(__('O motivo não pode ser excluído. Por favor, tente novamente.'));
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

            $motivo = $this->Motivos
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

            $motivo = $this->Motivos->patchEntity($motivo, [
                $campo => !$motivo->get($campo),
            ]);

            $this->Motivos->saveOrFail($motivo);

            $this->set(compact('motivo'));
            $this->set('_serialize', ['motivo']);
        } else {
            throw new BadRequestException('Método inválido!');
        }
    }
}
