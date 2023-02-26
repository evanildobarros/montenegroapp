<?php
declare(strict_types=1);

namespace App\Policy;

use Authorization\IdentityInterface;
use Cake\ORM\Query;

/**
 * RotasTable policy
 */
class RotasTablePolicy
{
    /**
     * @param \Authorization\IdentityInterface $user The user.
     * @param \Cake\ORM\Query $query The query.
     * @return \Cake\ORM\Query Return
     */
    public function scopeIndex(IdentityInterface $user, Query $query): Query
    {
        return $query->where(['Rotas.entregador_id' => $user->getIdentifier()]);
    }

    /**
     * @param \Authorization\IdentityInterface $user The user.
     * @param \Cake\ORM\Query $query The query.
     * @return \Cake\ORM\Query Return
     */
    public function scopeIniciar(IdentityInterface $user, Query $query): Query
    {
        return $query->where(['Rotas.entregador_id' => $user->getIdentifier()]);
    }

    /**
     * @param \Authorization\IdentityInterface $user The user.
     * @param \Cake\ORM\Query $query The query.
     * @return \Cake\ORM\Query Return
     */
    public function scopeFinalizar(IdentityInterface $user, Query $query): Query
    {
        return $query->where(['Rotas.entregador_id' => $user->getIdentifier()]);
    }
}
