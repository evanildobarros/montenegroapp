<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class ModificaTabelaNotificacoes extends AbstractMigration
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
        $notificacoes = $this->table('notificacoes');

        $notificacoes->drop()->save();

        $notificacoes
            ->addColumn('pessoa_id', 'integer', [
                'null' => true,
            ])
            ->addColumn('titulo', 'string', [
                'null' => false,
            ])
            ->addColumn('mensagem', 'text', [
                'null' => false,
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

        $notificacoes->create();

    }
}
