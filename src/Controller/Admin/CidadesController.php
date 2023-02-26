<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Entity\Cidade;
use Cake\Database\Expression\QueryExpression;
use Cake\Event\EventInterface;
use JansenFelipe\Utils\Utils;

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
    }

    /**
     * Index method
     * Retorna listagem de cidades
     *
     * @return void
     * @throws \Exception
     */
    public function index()
    {
        $this->getRequest()->allowMethod('ajax');
        $cidade_id = $this->getRequest()->getQuery('cidade_id');

        $cidades = $this->Cidades
            ->find()
            ->select([
                'id',
                'text' => "CONCAT( Cidades.nome, '/', Estados.sigla )",
            ])
            ->contain([
                'Estados',
            ])
            ->where(function (QueryExpression $expression) use ($cidade_id) {
                if (!empty($cidade_id)) {
                    $expression->eq('Cidades.id', $cidade_id);
                } else {
                    $valor = Utils::unaccents(mb_strtoupper($this->getRequest()->getQuery('q', ''))) . '%';
                    $expression->like('Cidades.nome', $valor);
                }

                return $expression;
            })
            ->limit(20);

        $this->set('results', $cidades);
        $this->set('_serialize', ['results']);
    }
}
