<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddStatusEmRotas extends AbstractMigration
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
        $rotas = $this->table('rotas');
        $rotas
            ->addColumn('status', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ]);
        $rotas->update();
    }
}
