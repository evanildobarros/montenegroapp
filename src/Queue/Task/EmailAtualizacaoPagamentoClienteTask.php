<?php
declare(strict_types=1);

namespace App\Queue\Task;

use Cake\Mailer\MailerAwareTrait;
use Queue\Queue\Task;

/**
 * AtualizacaoPagamentoCliente Task
 *
 * @property \App\Model\Table\PagamentosTable $Pagamentos
 */
class EmailAtualizacaoPagamentoClienteTask extends Task
{
    use MailerAwareTrait;

    /**
     * @param array $data The array passed to QueuedJobsTable::createJob()
     * @param int $jobId The id of the QueuedJob entity
     * @return void
     */
    public function run(array $data, int $jobId): void
    {
        $this->loadModel('Pagamentos');

        $pagamento_id = $data['pagamento_id'];

        $pagamento = $this->Pagamentos->get($pagamento_id, [
            'contain' => [
                'Pedidos' => [
                    'Pessoas',
                ],
            ],
        ]);

        $this->getMailer('Pedidos')->pagamentoStatusCliente($pagamento)->send();
    }
}
