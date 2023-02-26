<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddTabelaDispositivos extends AbstractMigration
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
        $dispositivos = $this->table('dispositivos');
        $dispositivos
            ->addColumn('pessoa_id', 'integer', [
                'null' => true,
            ])
            ->addColumn('id_dispositivo', 'string', [
                'null' => false,
            ])
            ->addColumn('firebase_token', 'text', [
                'null' => true,
            ])
            ->addColumn('created', 'timestamp', [
                'null' => true,
                'timezone' => true,
            ])
            ->addColumn('modified', 'timestamp', [
                'null' => true,
                'timezone' => true,
            ])
            ->addForeignKey('pessoa_id', 'pessoas', 'id', ['delete' => 'CASCADE']);

        $dispositivos->create();
    }
}
