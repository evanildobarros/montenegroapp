<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddCampoTratativaEmPedidos extends AbstractMigration
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
            ->addColumn('observacoes_tratativa_coleta', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('observacoes_tratativa_entrega', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('data_tratativa_coleta', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('data_tratativa_entrega', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ]);
        $pedidos->update();
    }
}
