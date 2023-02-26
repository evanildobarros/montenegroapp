<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AdicionaFkEntregaMeiosEmTabelaPrecos extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $this->table('tabela_precos')
            ->addForeignKey(
                'entrega_meio_id',
                'entrega_meios',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->save();
    }
}
