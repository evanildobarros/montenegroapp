<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddPrevisaoColetaEmPedidos extends AbstractMigration
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
            ->addColumn('previsao_coleta', 'date', [
                'comment' => 'Previsão para o objeto ser coletado pela MonteNegro',
                'default' => null,
                'limit' => null,
                'null' => true,
            ]);
        $pedidos->update();
    }
}
