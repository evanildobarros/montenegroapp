<?php
declare(strict_types=1);

namespace App\Model\Filter;

use Search\Model\Filter\FilterCollection;

class EntregaMeiosCollection extends FilterCollection
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        $this->value('id');
        $this->value('status');
        $this->add('nome', 'Search.Like', [
            'before' => true,
            'after' => true,
            'comparison' => 'LIKE',
        ]);
    }
}
