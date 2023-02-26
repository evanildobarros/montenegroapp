<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AdicionaParentIdEmRotaPedidos extends AbstractMigration
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
        $rotaPedidos = $this->table('rota_pedidos');
        $rotaPedidos
            ->addColumn('parent_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ]);
        $rotaPedidos->update();

        $rotaPedidos
            ->addForeignKey(
                'parent_id',
                'rota_pedidos',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->update();
    }
}
