<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AdicionaEnderecosEmPessoas extends AbstractMigration
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
        $pessoas = $this->table('pessoas');
        $pessoas
            ->removeColumn('cep')
            ->removeColumn('logradouro')
            ->removeColumn('numero')
            ->removeColumn('bairro')
            ->removeColumn('complemento')
            ->removeColumn('referencia')
            ->dropForeignKey('cidade_id')
            ->addColumn('endereco_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ]);
        $pessoas->update();

        $pessoas
            ->addForeignKey(
                'endereco_id',
                'enderecos',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->removeColumn('cidade_id');
        $pessoas->update();
    }
}
