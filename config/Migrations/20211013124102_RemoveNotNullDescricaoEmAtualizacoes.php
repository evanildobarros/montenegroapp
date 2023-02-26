<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveNotNullDescricaoEmAtualizacoes extends AbstractMigration
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
        $atualizacoes = $this->table('atualizacoes');
        $atualizacoes
            ->changeColumn('descricao', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ]);
        $atualizacoes->update();
    }
}
