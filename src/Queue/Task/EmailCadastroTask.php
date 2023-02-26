<?php
declare(strict_types=1);

namespace App\Queue\Task;

use Cake\Log\LogTrait;
use Cake\Mailer\MailerAwareTrait;
use Queue\Queue\Task;

/**
 * EmailCadastroTask
 *
 * @property \App\Model\Table\EmailsTable $Emails
 * @property \App\Model\Table\PessoasTable $Pessoas
 */
class EmailCadastroTask extends Task
{
    use MailerAwareTrait;
    use LogTrait;

    /**
     * @param array $data The array passed to QueuedJobsTable::createJob()
     * @param int $jobId The id of the QueuedJob entity
     * @return void
     * @throws \Exception
     */
    public function run(array $data, int $jobId): void
    {
        $this->loadModel('Emails');
        $this->loadModel('Pessoas');

        $conn = $this->Pessoas->getConnection();
        try {
            $conn->begin();
            $clientesMailer = $this->getMailer('Pessoas');

            $cliente = $this->Pessoas->get($data['cliente_id']);

            $email = $this->Emails->newEntity([
                'to_email' => $cliente->email,
                'to_name' => $cliente->nome,
                'subject' => 'MonteNegro - Confirmação de cadastro',
                'message' => '',
                'metadata' => serialize($clientesMailer),
                'message_opened' => false,
                'opening_date' => null,
            ]);

            $email = $this->Emails->saveOrFail($email);

            $clientesMailer = $clientesMailer->novo($cliente, $email);

            $email->metadata = serialize($clientesMailer);

            $this->Emails->saveOrFail($email);

            $clientesMailer->send();

            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollback();
            $this->log($e->getMessage());
            throw $e;
        }
    }
}
