<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class MudaTipoColunaPesos extends AbstractMigration
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
        $pesos = $this->table('pesos');
        $pesos
            ->changeColumn('peso_minimo', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->changeColumn('peso_maximo', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ]);
        $pesos->update();
    }
}
