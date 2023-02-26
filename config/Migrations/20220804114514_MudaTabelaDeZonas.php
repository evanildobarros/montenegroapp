<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class MudaTabelaDeZonas extends AbstractMigration
{
    /**
     * Up Method.
     *
     * @return void
     */
    public function up()
    {
        $tabela_precos = $this->table('tabela_precos');
        $tabela_precos
            ->dropForeignKey('entrega_meio_id')
            ->removeIndex(['entrega_meio_id', 'modalidade_distribuicao'])
            ->save();

        //-------------------------------------------------------------------------------------------------------

        $zonas = $this->table('zonas');
        $zonas
            ->changeColumn('nome', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('nome_abreviado', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('cidade_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->save();

        $zonas
            ->addForeignKey(
                'cidade_id',
                'cidades',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->save();

        //-------------------------------------------------------------------------------------------------------

        $cidadesZonas = $this->table('cidades_zonas');
        $cidadesZonas->dropForeignKey('zona_id')->save();
        $cidadesZonas->dropForeignKey('cidade_id')->save();
        $cidadesZonas->drop()->save();

        //-------------------------------------------------------------------------------------------------------

        $faixas = $this->table('faixas');
        $faixas
            ->addColumn('zona_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('inicio', 'text', [
                'null' => false,
            ])
            ->addColumn('fim', 'text', [
                'null' => false,
            ]);
        $faixas->save();

        $faixas
            ->addForeignKey(
                'zona_id',
                'zonas',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION',
                ]
            );
        $faixas->save();

        //-------------------------------------------------------------------------------------------------------

    }

    /**
     * Down Method.
     *
     * @return void
     */
    public function down()
    {
        //-------------------------------------------------------------------------------------------------------

        $zonas = $this->table('zonas');
        $zonas->dropForeignKey('cidade_id')->save();
        $zonas
            ->removeColumn('nome_abreviado')
            ->removeColumn('cidade_id')
            ->save();

        //-------------------------------------------------------------------------------------------------------

        $faixas = $this->table('faixas');
        $faixas->drop()->save();

        //-------------------------------------------------------------------------------------------------------

        $cidadesZonas = $this->table('cidades_zonas');

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

        //-------------------------------------------------------------------------------------------------------
    }
}
