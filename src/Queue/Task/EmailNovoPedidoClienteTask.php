<?php
declare(strict_types=1);

namespace App\Queue\Task;

use Cake\Mailer\MailerAwareTrait;
use Queue\Queue\Task;

/**
 * EmailNovoPedidoCliente task
 *
 * @property \App\Model\Table\PedidosTable $Pedidos
 */
class EmailNovoPedidoClienteTask extends Task
{
    use MailerAwareTrait;

    /**
     * @param array $data The array passed to QueuedJobsTable::createJob()
     * @param int $jobId The id of the QueuedJob entity
     * @return void
     */
    public function run(array $data, int $jobId): void
    {
        $this->loadModel('Pedidos');

        $pedido_id = $data['pedido_id'];

        $pedido = $this->Pedidos->get($pedido_id, [
            'contain' => [
                'Pessoas',
                'Objetos',
            ],
        ]);

        $this->getMailer('Pedidos')->novoPedidoCliente($pedido)->send();
    }
}
