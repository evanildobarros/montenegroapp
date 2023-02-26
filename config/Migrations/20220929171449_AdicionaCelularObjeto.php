<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AdicionaCelularObjeto extends AbstractMigration
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
        $this->table('objetos')
            ->addColumn('celular_destinatario', 'string', [
                'default' => null,
                'limit' => 65,
                'null' => true,
            ])
            ->changeColumn('telefone_destinatario', 'string', [
                'null' => true,
            ])
            ->update();
    }
}
