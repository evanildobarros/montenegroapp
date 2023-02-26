<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Cake\Database\Expression\QueryExpression;
use Cake\I18n\FrozenDate;

/**
 * Class FiliaisController
 *
 * @property \App\Model\Table\FiliaisTable $Filiais
 */
class FiliaisController extends AppController
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
    }

    /**
     * Index method
     *
     * @return void|null Renders view
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
            ])
            ->where(function (QueryExpression $expression) {
                return $expression->eq('Filiais.status', true);
            });

        $filiais = $this->paginate($query);

        $dias = (int)$this->Configs->parametro('prazo_envio');
        $dataBase = new FrozenDate();
        $prazoEnvio = $dataBase->addDays($dias);

        $results = [
            'success' => true,
            'data' => [
                'filiais' => $filiais,
                'prazo_envio' => $prazoEnvio,
            ],
        ];

        $this->set(compact('results'));
        $this->viewBuilder()->setOption('serialize', 'results');
    }
}
