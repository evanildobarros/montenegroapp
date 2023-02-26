<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use Cake\Database\Expression\QueryExpression;

/**
 * Configs Controller
 *
 * @property \App\Model\Table\ConfigsTable $Configs
 * @property \App\Model\Table\UsersTable $Users
 * @property \Search\Controller\Component\SearchComponent $Search
 * @method \App\Model\Entity\Config[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ConfigsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        if ($this->getRequest()->is('post')) {
            $configsData = $this->getRequest()->getData('configs');
            $configs = [];
            foreach ($configsData as $index => $configData) {
                $config = $this->Configs
                    ->find()
                    ->where(function (QueryExpression $expression) use ($configData) {
                        $expression->eq('Configs.parametro', $configData['parametro']);

                        return $expression;
                    })
                    ->first();

                if (empty($config)) {
                    $config = $this->Configs->newEntity(['parametro' => $configData['parametro']]);
                }

                if (is_array($configData['valor'])) {
                    if (!empty($configData['valor']['tmp_name'])) {
                        $config->valor = base64_encode(file_get_contents($configData['valor']['tmp_name']));
                    }
                } else {
                    $config->valor = $configData['valor'];
                }
                $configs[] = $config;
            }

            $this->Configs->saveMany($configs);

            $this->Flash->success('Configurações salvas!');

            return $this->redirect(['action' => 'index']);
        }

        $configs = $this->Configs->parametros();

        $this->set(compact('configs'));
    }
}
