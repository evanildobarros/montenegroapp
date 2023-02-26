<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddCampoIconeEmEntregaMeios extends AbstractMigration
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
        $entrega_meios = $this->table('entrega_meios');

        $entrega_meios
            ->addColumn('icone', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ]);

        $entrega_meios->update();
    }
}
