<?php
declare(strict_types=1);

namespace AuditLog\Test\App\Model\Table;

use Cake\ORM\Table;

class AuthorsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->hasMany('Articles', [
            'foreignKey' => 'author_id',
        ]);

        $this->addBehavior('AuditLog.Auditable');
    }
}
