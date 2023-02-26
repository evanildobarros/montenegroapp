<?php
declare(strict_types=1);

namespace App\Queue\Task;

use Cake\Mailer\MailerAwareTrait;
use Queue\Queue\Task;

/**
 * EmailEsqueciSenha Task
 *
 * @property \App\Model\Table\PessoasTable $Pessoas
 */
class EmailEsqueciSenhaTask extends Task
{
    use MailerAwareTrait;

    /**
     * @param array $data The array passed to QueuedJobsTable::createJob()
     * @param int $jobId The id of the QueuedJob entity
     * @return void
     */
    public function run(array $data, int $jobId): void
    {
        $this->loadModel('Pessoas');

        $pessoa_id = $data['pessoa_id'];

        $pessoa = $this->Pessoas->get($pessoa_id);

        $this->getMailer('Pessoas')->esqueciSenha($pessoa)->send();
    }
}
