<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveTableEntregaModalidades extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $entregaModalidades = $this->table('entrega_modalidades');
        $pedidos = $this->table('pedidos');

        $pedidos->dropForeignKey('entrega_modalidade_id')->save();
        $pedidos
            ->removeColumn('entrega_modalidade_id')
            ->removeColumn('modalidade_entrega');
        $pedidos->update();

        $entregaModalidades->drop()->save();
    }
}
