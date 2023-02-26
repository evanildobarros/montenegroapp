<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class MudaEntregaTentativas extends AbstractMigration
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
        $entrega_tentativas = $this->table('entrega_tentativas');
        $entrega_tentativas
            ->dropForeignKey(
                'pedido_id'
            )
            ->dropForeignKey(
                'motivo_id'
            )
            ->dropForeignKey(
                'entregador_id'
            )
            ->save();
        $entrega_tentativas->drop()->save();

        $tentativas = $this->table('tentativas');
        $tentativas
            ->addColumn('rota_pedido_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('motivo_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('nome_motivo', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('observacoes', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $tentativas
            ->addForeignKey(
                'rota_pedido_id',
                'rota_pedidos',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->addForeignKey(
                'motivo_id',
                'motivos',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->update();
    }
}
