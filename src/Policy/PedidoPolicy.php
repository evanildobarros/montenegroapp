<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Pedido;
use App\Model\Table\PessoasTable;
use Authorization\IdentityInterface;
use Authorization\Policy\Result;

/**
 * Pedido policy
 */
class PedidoPolicy
{
    /**
     * Check if $user can view Pedido
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Pedido $pedido Entity Pessoa
     * @return \Authorization\Policy\Result result
     */
    public function canView(IdentityInterface $user, Pedido $pedido)
    {
        if ($user->model === PessoasTable::CLIENTE && $pedido->cliente_id === $user->id) {
            return new Result(true);
        }

        return new Result(false, 'Permissão negada');
    }

    /**
     * Check if $user can view Pedido Recusado
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Pedido $pedido Entity Pessoa
     * @return \Authorization\Policy\Result result
     */
    public function canRecusado(IdentityInterface $user, Pedido $pedido)
    {
        if ($user->model === PessoasTable::CLIENTE && $pedido->cliente_id === $user->id) {
            return new Result(true);
        }

        return new Result(false, 'Permissão negada');
    }
}
