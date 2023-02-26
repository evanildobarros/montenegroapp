<?php
declare(strict_types=1);

namespace App\Mailer\Preview;

use DebugKit\Mailer\MailPreview;

/**
 * PedidosMailPreview class
 *
 * @property \App\Model\Table\PagamentosTable $Pagamentos
 * @property \App\Model\Table\PedidosTable $Pedidos
 */
class PedidosMailPreview extends MailPreview
{
    /**
     * Testa o email de pedido confirmado para o cliente
     *
     * @return \App\Mailer\ClientesMailer
     */
    public function pedidoConfirmadoCliente()
    {
        $this->loadModel('Pagamentos');

        /** @var \App\Model\Entity\Pagamento $pagamento */
        $pagamento = $this->Pagamentos
            ->find()
            ->contain([
                'Faturas' => [
                    'Pedidos' => [
                        'Clientes',
                    ],
                ],
            ])
            ->first();

        return $this->getMailer('Pedidos')->pedidoConfirmadoCliente($pagamento);
    }

    /**
     * Testa o email de pedido para o admin
     *
     * @return \App\Mailer\ClientesMailer
     */
    public function pedidoStatusAdmin()
    {
        $this->loadModel('Pagamentos');

        /** @var \App\Model\Entity\Pagamento $pagamento */
        $pagamento = $this->Pagamentos
            ->find()
            ->contain([
                'Faturas' => [
                    'Pedidos' => [
                        'Clientes',
                    ],
                ],
            ])
            ->first();

        return $this->getMailer('Pedidos')->pedidoStatusAdmin($pagamento);
    }

    /**
     * Testa o email de novo pedido para o cliente
     *
     * @return \App\Mailer\ClientesMailer
     */
    public function novoPedidoCliente()
    {
        $this->loadModel('Pedidos');

        /** @var \App\Model\Entity\Pedido $pedido */
        $pedido = $this->Pedidos
            ->find()
            ->contain([
                'Clientes',
                'Itens' => [
                    'Produtos',
                ],
            ])
            ->first();

        return $this->getMailer('Pedidos')->novoPedidoCliente($pedido);
    }

    /**
     * Testa o email de novo pedido para o admin
     *
     * @return \App\Mailer\ClientesMailer
     */
    public function novoPedidoAdmin()
    {
        $this->loadModel('Pedidos');

        /** @var \App\Model\Entity\Pedido $pedido */
        $pedido = $this->Pedidos
            ->find()
            ->contain([
                'Clientes',
                'Itens' => [
                    'Produtos',
                ],
            ])
            ->first();

        return $this->getMailer('Pedidos')->novoPedidoAdmin($pedido);
    }
}
