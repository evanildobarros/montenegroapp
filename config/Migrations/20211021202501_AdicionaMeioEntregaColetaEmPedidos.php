<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AdicionaMeioEntregaColetaEmPedidos extends AbstractMigration
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
            ->addColumn('coleta_meio_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('meio_coleta', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ]);
        $pedidos->update();

        $pedidos
            ->addForeignKey(
                'coleta_meio_id',
                'entrega_meios',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->update();
    }
}
