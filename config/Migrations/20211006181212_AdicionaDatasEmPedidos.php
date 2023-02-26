<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AdicionaDatasEmPedidos extends AbstractMigration
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
        $objetos = $this->table('objetos');
        $objetos->removeColumn('data_postagem');
        $objetos->update();


        $pedidos = $this->table('pedidos');
        $pedidos
            ->addColumn('data_chegada', 'datetime', [
                'comment' => 'Data que o objeto chegou no Monte Negro',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('data_postagem', 'datetime', [
                'comment' => 'Data da primeira rota de entrega do objeto',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('data_entrega', 'datetime', [
                'comment' => 'Data que o objeto foi entregue ao destino definido',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('entregue_mesma_rota', 'boolean', [
                'comment' => 'Se a modalidade de distribuição for coleta, então marcar TRUE se ' .
                    'o objeto será entregue na mesma rota ou FALSE se não for entregue, voltando para o centro ' .
                    'de distribuição',
                'default' => null,
                'limit' => null,
                'null' => true,
            ]);
        $pedidos->update();
    }
}
