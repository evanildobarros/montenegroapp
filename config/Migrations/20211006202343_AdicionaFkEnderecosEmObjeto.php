<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AdicionaFkEnderecosEmObjeto extends AbstractMigration
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
            ->removeColumn('cep')
            ->removeColumn('logradouro')
            ->removeColumn('numero')
            ->removeColumn('bairro')
            ->removeColumn('complemento')
            ->removeColumn('referencia');

        $objetos->dropForeignKey('cidade_id')->save();
        $objetos->removeColumn('cidade_id');

        $objetos
            ->addColumn('endereco_entrega_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('endereco_coleta_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ]);
        $objetos->update();

        $objetos
            ->addForeignKey(
                'endereco_entrega_id',
                'enderecos',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->addForeignKey(
                'endereco_coleta_id',
                'enderecos',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->update();
    }
}
