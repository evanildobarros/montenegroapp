<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Cake\Event\EventInterface;

/**
 * Class CidadesController
 *
 * @property \App\Model\Table\CidadesTable $Cidades
 */
class CidadesController extends AppController
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->Crud->setConfig('actions', $this->Crud->normalizeArray([
            'Crud.Index',
        ]));
        $this->Authentication->allowUnauthenticated([
            'index',
        ]);
    }

    /**
     * Index method
     * Retorna listagem de cidades para os clientes
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function index()
    {
        $this->Crud->on('beforePaginate', function (EventInterface $event) {
            /** @var \Cake\ORM\Query $query */
            $query = $event->getSubject()->query;
            $query->contain('Estados');
        });

        return $this->Crud->execute();
    }
}
