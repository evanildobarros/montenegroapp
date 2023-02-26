<?php
declare(strict_types=1);

namespace App\Mailer;

use App\Model\Entity\Atualizacao;
use App\Model\Entity\Pagamento;
use App\Model\Entity\Pedido;
use Cake\Mailer\Mailer;

/**
 * Pedidos mailer.
 *
 * @property \App\Model\Table\ConfigsTable $Configs
 */
class PedidosMailer extends Mailer
{
    /**
     * Mailer's name.
     *
     * @var string
     */
    public static $name = 'Pedidos';

    /**
     * @inheritDoc
     */
    public function __construct($config = null)
    {
        parent::__construct($config);

        $this->loadModel('Configs');
    }

    /**
     * @param \App\Model\Entity\Pedido $pedido Pedido
     * @return $this
     */
    public function novoPedidoCliente(Pedido $pedido)
    {
        $this
            ->setViewVars([
                'pedido' => $pedido,
            ])
            ->viewBuilder()
            ->setTemplate('novoPedidoCliente');

        $this
            ->setEmailFormat('html')
            ->setFrom('naoresponder@montenegroexpress.com.br', 'Montenegro Express')
            ->setTo($pedido->pessoa->email, $pedido->pessoa->nome)
            ->setSubject('Novo Pedido - MonteNegro');

        return $this;
    }

    /**
     * @param \App\Model\Entity\Pedido $pedido Pedido
     * @return $this
     */
    public function novoPedidoAdmin(Pedido $pedido)
    {
        $this
            ->setViewVars([
                'pedido' => $pedido,
            ])
            ->viewBuilder()
            ->setTemplate('novoPedidoAdmin');

        $this
            ->setEmailFormat('html')
            ->setFrom('naoresponder@montenegroexpress.com.br', 'Montenegro Express')
            ->setTo($this->Configs->parametro('email_pedidos'))
            ->setSubject('Novo Pedido - MonteNegro');

        return $this;
    }

    /**
     * @param \App\Model\Entity\Atualizacao $atualizacao Atualizacao
     * @return $this
     */
    public function pedidoAtualizacaoCliente(Atualizacao $atualizacao)
    {
        $this
            ->setViewVars([
                'atualizacao' => $atualizacao,
            ])
            ->viewBuilder()
            ->setTemplate('pedidoAtualizacaoCliente');

        $this
            ->setEmailFormat('html')
            ->setFrom('naoresponder@montenegroexpress.com.br', 'Montenegro Express')
            ->setTo($atualizacao->pedido->pessoa->email, $atualizacao->pedido->pessoa->nome)
            ->setSubject('Atualização Pedido - MonteNegro');

        return $this;
    }

    /**
     * @param \App\Model\Entity\Pagamento $pagamento Pagamento
     * @return $this
     */
    public function pagamentoStatusCliente(Pagamento $pagamento)
    {
        $this
            ->setViewVars([
                'pagamento' => $pagamento,
            ])
            ->viewBuilder()
            ->setTemplate('pagamentoStatusCliente');

        $this
            ->setEmailFormat('html')
            ->setFrom('naoresponder@montenegroexpress.com.br', 'Montenegro Express')
            ->setTo($pagamento->pedido->pessoa->email, $pagamento->pedido->pessoa->nome)
            ->setSubject('Atualização Pedido - MonteNegro');

        return $this;
    }
}
