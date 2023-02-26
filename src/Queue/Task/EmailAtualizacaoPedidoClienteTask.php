<?php
declare(strict_types=1);

namespace App\Queue\Task;

use Cake\Mailer\MailerAwareTrait;
use Queue\Queue\Task;

/**
 * EmailAtualizacaoPedidoCliente Task
 *
 * @property \App\Model\Table\AtualizacoesTable $Atualizacoes
 */
class EmailAtualizacaoPedidoClienteTask extends Task
{
    use MailerAwareTrait;

    /**
     * @param array $data The array passed to QueuedJobsTable::createJob()
     * @param int $jobId The id of the QueuedJob entity
     * @return void
     */
    public function run(array $data, int $jobId): void
    {
        $this->loadModel('Atualizacoes');

        $atualizacao_id = $data['atualizacao_id'];

        $atualizacao = $this->Atualizacoes->get($atualizacao_id, [
            'contain' => [
                'Pedidos' => [
                    'Pessoas',
                ],
            ],
        ]);

        $this->getMailer('Pedidos')->pedidoAtualizacaoCliente($atualizacao)->send();
    }
}
