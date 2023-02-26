<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Cake\Event\EventInterface;

/**
 * Class MotivosController
 *
 * @property \App\Model\Table\MotivosTable $Motivos
 */
class MotivosController extends AppController
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
            $query
                ->where(['status' => true])
                ->orderAsc('nome');
        });

        return $this->Crud->execute();
    }
}
