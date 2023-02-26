<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveColunasCartaoEmPedidos extends AbstractMigration
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
        $pedidos = $this->table('pedidos');
        $pedidos
            ->removeColumn('token')
            ->removeColumn('bandeira')
            ->removeColumn('bin')
            ->removeColumn('ultimo_quatro_digitos');
        $pedidos->update();
    }
}
