<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Pessoa;
use App\Model\Table\PessoasTable;
use Authorization\IdentityInterface;
use Authorization\Policy\Result;

/**
 * Pessoa policy
 */
class PessoaPolicy
{
    /**
     * Check if $user can edit Cliente
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Pessoa $pessoa Entity Pessoa
     * @return \Authorization\Policy\Result result
     */
    public function canEdit(IdentityInterface $user, Pessoa $pessoa)
    {
        if ($user->model === PessoasTable::CLIENTE && ($pessoa->id === $user->id)) {
            return new Result(true);
        }

        return new Result(false, 'Permissão negada');
    }
}
