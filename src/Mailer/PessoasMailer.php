<?php
declare(strict_types=1);

namespace App\Mailer;

use App\Model\Entity\Email;
use App\Model\Entity\Pessoa;
use Cake\Mailer\Mailer;
use Cake\ORM\Locator\TableLocator;
use Cake\Utility\Text;

/**
 * Pessoas mailer.
 */
class PessoasMailer extends Mailer
{
    /**
     * Mailer's name.
     *
     * @var string
     */
    public static $name = 'Pessoas';

    /**
     * Dispara email de esqueci senha
     *
     * @param \App\Model\Entity\Pessoa $pessoa Pessoa
     * @return $this
     */
    public function esqueciSenha(Pessoa $pessoa)
    {
        $tableLocator = new TableLocator();
        $pessoasTable = $tableLocator->get('Pessoas');

        $pessoa->token = hash('md5', Text::uuid());

        /** @var \App\Model\Entity\Pessoa $pessoa */
        $pessoa = $pessoasTable->saveOrFail($pessoa);

        $this
            ->setViewVars([
                'pessoa' => $pessoa,
            ])
            ->viewBuilder()
            ->setTemplate('esqueciSenha');

        $this
            ->setEmailFormat('html')
            ->setFrom('naoresponder@montenegroexpress.com.br', 'Montenegro Express')
            ->setTo($pessoa->email, $pessoa->nome)
            ->setSubject('Monte Negro - Redefinição de senha');

        return $this;
    }

    /**
     * Dispara email de boas vindas e confirmação de registro para o usuário
     *
     * @param \App\Model\Entity\Pessoa $pessoa Pessoa
     * @param \App\Model\Entity\Email $email Email enviado
     * @return $this
     */
    public function novo(Pessoa $pessoa, Email $email)
    {
        $tableLocator = new TableLocator();
        $pessoasTable = $tableLocator->get('Pessoas');

        $pessoa->token_ativacao = Text::uuid();
        /** @var \App\Model\Entity\Pessoa $pessoa */
        $pessoa = $pessoasTable->saveOrFail($pessoa);

        $this
            ->setViewVars([
                'pessoa' => $pessoa,
                'email' => $email,
            ])
            ->viewBuilder()
            ->setTemplate('novo');

        $this
            ->setEmailFormat('html')
            ->setFrom('naoresponder@montenegroexpress.com.br', 'Montenegro Express')
            ->setTo($pessoa->email, $pessoa->nome)
            ->setSubject('MonteNegro - Confirmação de cadastro');

        return $this;
    }
}
