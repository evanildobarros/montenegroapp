<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Cake\Database\Expression\QueryExpression;

/**
 * Class NotificacoesController
 *
 * @property \App\Model\Table\NotificacoesTable $Notificacoes
 */
class NotificacoesController extends AppController
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
    }

    /**
     * Info method
     * Retorna listagem das últimas 15 notificações
     *
     * @return void
     * @throws \Exception
     */
    public function info()
    {
        $notificacoes = $this->Notificacoes
            ->find()
            ->where(function (QueryExpression $expression) {
                $expression
                    ->eq('Notificacoes.pessoa_id', $this->Authentication->getIdentity()->getIdentifier());

                return $expression;
            })
            ->orderDesc('Notificacoes.created')
            ->limit(15)
            ->toArray();

        $result = [
            'success' => true,
            'data' => $notificacoes,
        ];

        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', 'result');
    }
}
