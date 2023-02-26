<?php
declare(strict_types=1);

namespace App\Mailer;

use App\Model\Entity\Rota;
use Cake\Mailer\Mailer;

/**
 * Rotas mailer.
 *
 * @property \App\Model\Table\ConfigsTable $Configs
 */
class RotasMailer extends Mailer
{
    /**
     * Mailer's name.
     *
     * @var string
     */
    public static $name = 'Rotas';

    /**
     * @inheritDoc
     */
    public function __construct($config = null)
    {
        parent::__construct($config);

        $this->loadModel('Configs');
    }

    /**
     * @param \App\Model\Entity\Rota $rota Rota
     * @return $this
     */
    public function rotaAtualizacaoAdmin(Rota $rota)
    {
        $this
            ->setViewVars([
                'rota' => $rota,
            ])
            ->viewBuilder()
            ->setTemplate('rotaAtualizacaoAdmin');

        $this
            ->setEmailFormat('html')
            ->setFrom('naoresponder@montenegroexpress.com.br', 'Montenegro Express')
            ->setTo($this->Configs->parametro('email_rotas'))
            ->setSubject('Atualização Rota - MonteNegro');

        return $this;
    }
}
