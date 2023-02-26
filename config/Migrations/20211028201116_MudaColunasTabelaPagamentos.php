<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class MudaColunasTabelaPagamentos extends AbstractMigration
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
            ->addColumn('valor', 'decimal', [
                'null' => false,
                'precision' => 9,
                'scale' => 2,
            ])
            ->removeColumn('data')
            ->changeColumn('status', 'tinyinteger', [
                'null' => false,
            ]);
        $pagamentos->update();
    }
}
