<?php
declare(strict_types=1);

namespace App\Queue\Task;

use Cake\Mailer\MailerAwareTrait;
use Queue\Queue\Task;

/**
 * EmailAtualizacaoRotaAdmin Task
 *
 * @property \App\Model\Table\RotasTable $Rotas
 */
class EmailAtualizacaoRotaAdminTask extends Task
{
    use MailerAwareTrait;

    /**
     * @param array $data The array passed to QueuedJobsTable::createJob()
     * @param int $jobId The id of the QueuedJob entity
     * @return void
     */
    public function run(array $data, int $jobId): void
    {
        $this->loadModel('Rotas');

        $rota_id = $data['rota_id'];

        $rota = $this->Rotas->get($rota_id, [
            'contain' => [
                'Pessoas',
            ],
        ]);

        $this->getMailer('Rotas')->rotaAtualizacaoAdmin($rota)->send();
    }
}
