<?php
declare(strict_types=1);

namespace App\Model\Filter;

use Search\Model\Filter\FilterCollection;

class UsersCollection extends FilterCollection
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        $this->value('id');
        $this->value('group_id');
        $this->add('username', 'Search.Like', [
            'before' => true,
            'after' => true,
            'comparison' => 'LIKE',
        ]);
        $this->add('nome', 'Search.Like', [
            'before' => true,
            'after' => true,
            'comparison' => 'LIKE',
        ]);
        $this->boolean('status');
    }
}
