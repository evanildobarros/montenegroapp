<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class MudaTipoCampoEmMeiosEntregas extends AbstractMigration
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
        $entregaMeios = $this->table('entrega_meios');
        $entregaMeios
            ->changeColumn('altura_maxima', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->changeColumn('largura_maxima', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->changeColumn('profundidade_maxima', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ]);
        $entregaMeios->update();
    }
}
