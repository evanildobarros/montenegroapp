<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddColunaModelPessoas extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function change()
    {
        $pessoas = $this->table('pessoas');
        $pessoas->renameColumn('tipo', 'model')->save();
        $pessoas
            ->addColumn('tipo', 'char', [
                'default' => null,
                'limit' => 2,
                'null' => true,
            ]);
        $pessoas->update();
    }
}
