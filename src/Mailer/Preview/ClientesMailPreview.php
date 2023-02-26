<?php
declare(strict_types=1);

namespace App\Mailer\Preview;

use DebugKit\Mailer\MailPreview;

/**
 * ClientesMailPreview class
 *
 * @property \App\Model\Table\ClientesTable $Clientes
 * @property \App\Model\Table\EmailsTable $Emails
 */
class ClientesMailPreview extends MailPreview
{
    /**
     * Testa o email de novo cliente
     *
     * @return \App\Mailer\ClientesMailer
     */
    public function novo()
    {
        $this->loadModel('Pessoas');
        $this->loadModel('Emails');

        /** @var \App\Model\Entity\Pessoa $cliente */
        $cliente = $this->Clientes->find()->first();
        /** @var \App\Model\Entity\Email $email */
        $email = $this->Emails->find()->first();

        return $this->getMailer('Pessoas')->novo($cliente, $email);
    }
}
