<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Entity\Group;
use App\Model\Table\GroupsTable;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Exception\BadRequestException;

/**
 * Groups Controller
 *
 * @property \App\Model\Table\GroupsTable $Groups
 * @property \Search\Controller\Component\SearchComponent $Search
 * @method \App\Model\Entity\Group[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GroupsController extends AppController
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
        $query = $this->Groups
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ]);
        $groups = $this->paginate($query);

        $isSearch = $this->Groups->isSearch();
        $this->set(compact('groups', 'isSearch'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $group = $this->Groups->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $conn = $this->Groups->getConnection();
            try {
                $conn->begin();
                $group = $this->Groups->patchEntity($group, $this->getRequest()->getData());
                $group->painel = GroupsTable::ADMIN;
                $this->Groups->saveOrFail($group);

                $conn->commit();
                $this->Flash->success(__('O grupo foi salvo com sucesso.'));

                return $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->logException($e);
                $this->Flash->error(__('O grupo não pode ser salvo. Por favor, tente novamente.'));
            }
        }
        $this->set(compact('group'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Group id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $group = $this->Groups->get($id, [
            'contain' => [],
        ]);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $conn = $this->Groups->getConnection();
            try {
                $conn->begin();
                $group = $this->Groups->patchEntity($group, $this->getRequest()->getData());
                $this->Groups->saveOrFail($group);

                $conn->commit();
                $this->Flash->success(__('O grupo foi salvo com sucesso.'));

                return $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                $conn->rollback();
                $this->logException($e);
                $this->Flash->error(__('O grupo não pode ser salvo. Por favor, tente novamente.'));
            }
        }
        $this->set(compact('group'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Group id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->Groups->getConnection();
        try {
            $conn->begin();
            $group = $this->Groups->get($id);
            $this->Groups->deleteOrFail($group);
            $conn->commit();
            $this->Flash->success(__('O grupo foi excluído com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->logException($e);
            $this->Flash->error(__('O grupo não pode ser excluído. Por favor, tente novamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * DeleteAll method
     *
     * @param string|null $ids Group id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteAll($ids = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $conn = $this->Groups->getConnection();
        try {
            $conn->begin();
            $this->Groups
                ->find()
                ->where(function (QueryExpression $expression) use ($ids) {
                    $expression->in('Groups.id', explode('|', $ids));

                    return $expression;
                })
                ->each(function (Group $group) {
                    $this->Groups->deleteOrFail($group);
                });
            $conn->commit();
            $this->Flash->success(__('O grupo foi excluído com sucesso.'));
        } catch (\Exception $e) {
            $conn->rollback();
            $this->logException($e);
            $this->Flash->error(__('O grupo não pode ser excluído. Por favor, tente novamente.'));
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

            $group = $this->Groups
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

            $group = $this->Groups->patchEntity($group, [
                $campo => !$group->get($campo),
            ]);

            $this->Groups->saveOrFail($group);

            $this->set(compact('group'));
            $this->set('_serialize', ['group']);
        } else {
            throw new BadRequestException('Método inválido!');
        }
    }
}
