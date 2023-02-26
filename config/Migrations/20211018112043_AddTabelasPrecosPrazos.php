<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddTabelasPrecosPrazos extends AbstractMigration
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
        $tabelaPrecos = $this->table('tabela_precos');
        $tabelaPrecosZonas = $this->table('tabela_precos_zonas');
        $zonas = $this->table('zonas');
        $cidadesZonas = $this->table('cidades_zonas');
        $pesos = $this->table('pesos');
        $taxas = $this->table('taxas');

        $zonas
            ->addColumn('nome', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $tabelaPrecos
            ->addColumn('entrega_meio_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('nome', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('modalidade_distribuicao', 'string', [
                'comment' => 'Coleta, Entrega (centro de distribuicao)',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'entrega_meio_id',
                    'modalidade_distribuicao',
                ],
                ['unique' => true]
            )
            ->create();

        $tabelaPrecos
            ->addForeignKey(
                'entrega_meio_id',
                'entrega_meios',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->update();

        $tabelaPrecosZonas
            ->addColumn('tabela_preco_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('zona_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $tabelaPrecosZonas
            ->addForeignKey(
                'tabela_preco_id',
                'tabela_precos',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->addForeignKey(
                'zona_id',
                'zonas',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->update();

        $cidadesZonas
            ->addColumn('cidade_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('zona_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $cidadesZonas
            ->addForeignKey(
                'cidade_id',
                'cidades',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->addForeignKey(
                'zona_id',
                'zonas',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->update();

        $pesos
            ->addColumn('tabela_preco_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('peso_minimo', 'decimal', [
                'default' => null,
                'null' => true,
                'precision' => 12,
                'scale' => 4,
            ])
            ->addColumn('peso_maximo', 'decimal', [
                'default' => null,
                'null' => true,
                'precision' => 12,
                'scale' => 4,
            ])
            ->addColumn('quilo_adicional', 'boolean', [
                'comment' => 'Quando for TRUE, peso minimo e maximo ficam nulos',
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $pesos
            ->addForeignKey(
                'tabela_preco_id',
                'tabela_precos',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->update();

        $taxas
            ->addColumn('peso_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('zona_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('valor', 'decimal', [
                'default' => null,
                'null' => false,
                'precision' => 9,
                'scale' => 2,
            ])
            ->addColumn('tempo_estimado', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $taxas
            ->addForeignKey(
                'peso_id',
                'pesos',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->addForeignKey(
                'zona_id',
                'zonas',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->update();
    }
}
