<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AdicionaTipoEmRotaPedidos extends AbstractMigration
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
        $rota_pedidos = $this->table('rota_pedidos');
        $rota_pedidos
            ->addColumn('tipo', 'string', [
                'comment' => 'Coleta ou entrega',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ]);
        $rota_pedidos->update();
    }
}
