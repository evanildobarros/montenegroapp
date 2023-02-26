<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CorrigeErrosOrtograficos extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function up()
    {
        $cidades = $this->table('cidades');
        $cidades->renameColumn('população', 'populacao');
        $cidades->update();

        $atualizacaoes = $this->table('atualizacaoes');
        $atualizacaoes->dropForeignKey('pedido_id')->save();
        $atualizacaoes->drop()->save();

        $atualizacoes = $this->table('atualizacoes');
        $atualizacoes
            ->addColumn('pedido_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('titulo', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('descricao', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('data', 'datetime', [
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
            ]);
        $atualizacoes->create();

        $atualizacoes
            ->addForeignKey(
                'pedido_id',
                'pedidos',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            );
        $atualizacoes->update();
    }
}
