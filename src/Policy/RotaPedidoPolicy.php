<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\RotaPedido;
use App\Model\Table\PessoasTable;
use Authorization\IdentityInterface;
use Authorization\Policy\Result;

/**
 * RotaPedido policy
 */
class RotaPedidoPolicy
{
    /**
     * Check if $user can view RotaPedido
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\RotaPedido $rotaPedido Entity RotaPedido
     * @return \Authorization\Policy\Result result
     */
    public function canView(IdentityInterface $user, RotaPedido $rotaPedido)
    {
        if ($user->model === PessoasTable::ENTREGADOR && $rotaPedido->rota->entregador_id === $user->id) {
            return new Result(true);
        }

        return new Result(false, 'Permissão negada');
    }

    /**
     * Check if $user can entregar Objeto
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\RotaPedido $rotaPedido Entity RotaPedido
     * @return \Authorization\Policy\Result result
     */
    public function canEntregar(IdentityInterface $user, RotaPedido $rotaPedido)
    {
        if ($user->model === PessoasTable::ENTREGADOR && $rotaPedido->rota->entregador_id === $user->id) {
            return new Result(true);
        }

        return new Result(false, 'Permissão negada');
    }
}
