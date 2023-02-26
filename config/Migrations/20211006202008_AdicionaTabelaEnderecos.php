<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AdicionaTabelaEnderecos extends AbstractMigration
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
        $enderecos = $this->table('enderecos');
        $enderecos
            ->addColumn('cidade_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('cep', 'string', [
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('logradouro', 'string', [
                'null' => false,
            ])
            ->addColumn('numero', 'string', [
                'null' => false,
            ])
            ->addColumn('bairro', 'string', [
                'null' => false,
            ])
            ->addColumn('complemento', 'string', [
                'null' => true,
            ])
            ->addColumn('referencia', 'string', [
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'null' => true,
            ]);
        $enderecos->create();

        $enderecos
            ->addForeignKey(
                'cidade_id',
                'cidades',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            );
        $enderecos->update();
    }
}
