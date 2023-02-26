<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class MudaTipoCampoDataEmPagamentos extends AbstractMigration
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
        $pagamentos = $this->table('pagamentos');
        $pagamentos
            ->changeColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->changeColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ]);
        $pagamentos->update();
    }
}
