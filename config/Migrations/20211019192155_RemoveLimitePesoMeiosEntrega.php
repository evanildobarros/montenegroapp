<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveLimitePesoMeiosEntrega extends AbstractMigration
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
        $meios_entrega = $this->table('entrega_meios');
        $meios_entrega
            ->removeColumn('peso_limite')
            ->addColumn('altura_maxima', 'decimal', [
                'default' => null,
                'null' => true,
                'precision' => 12,
                'scale' => 3,
            ])
            ->addColumn('largura_maxima', 'decimal', [
                'default' => null,
                'null' => true,
                'precision' => 12,
                'scale' => 3,
            ])
            ->addColumn('profundidade_maxima', 'decimal', [
                'default' => null,
                'null' => true,
                'precision' => 12,
                'scale' => 3,
            ]);
        $meios_entrega->update();
    }
}
