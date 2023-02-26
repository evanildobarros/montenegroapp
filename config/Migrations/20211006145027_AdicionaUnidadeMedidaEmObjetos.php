<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AdicionaUnidadeMedidaEmObjetos extends AbstractMigration
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
        $objetos
            ->addColumn('unidade_medida_comprimento', 'string', [
                'default' => null,
                'limit' => 2,
                'null' => true,
            ])
            ->addColumn('unidade_medida_peso', 'string', [
                'default' => null,
                'limit' => 2,
                'null' => true,
            ]);
        $objetos->update();
    }
}
