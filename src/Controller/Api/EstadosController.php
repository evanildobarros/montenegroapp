<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Class EstadosController
 *
 * @property \App\Model\Table\EstadosTable $Estados
 */
class EstadosController extends AppController
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
        $this->Authentication->allowUnauthenticated(['index']);
    }
}
