<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveCodigoPedidos extends AbstractMigration
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
            ->removeColumn('codigo')
            ->removeColumn('codigo_rastreio')
            ->renameColumn('observacoes', 'instrucoes');
        $pedidos->update();
    }
}
