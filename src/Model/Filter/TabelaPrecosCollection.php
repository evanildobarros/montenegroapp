<?php
declare(strict_types=1);

namespace App\Model\Filter;

use Search\Model\Filter\FilterCollection;

class TabelaPrecosCollection extends FilterCollection
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        $this->value('id');
        $this->value('entrega_meio_id');
        $this->add('modalidade_distribuicao', 'Search.Like', [
            'before' => true,
            'after' => true,
            'comparison' => 'LIKE',
        ]);
        $this->add('nome', 'Search.Like', [
            'before' => true,
            'after' => true,
            'comparison' => 'LIKE',
        ]);
    }
}
