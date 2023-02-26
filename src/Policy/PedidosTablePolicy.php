<?php
declare(strict_types=1);

namespace App\Policy;

use Authorization\IdentityInterface;
use Cake\ORM\Query;

/**
 * PedidosTable policy
 */
class PedidosTablePolicy
{
    /**
     * @param \Authorization\IdentityInterface $user The user.
     * @param \Cake\ORM\Query $query The query.
     * @return \Cake\ORM\Query Return
     */
    public function scopeIndex(IdentityInterface $user, Query $query): Query
    {
        return $query->where(['Pedidos.cliente_id' => $user->getIdentifier()]);
    }
}
