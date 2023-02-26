<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AdicionaTableEmails extends AbstractMigration
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
        $emails = $this->table('emails', ['id' => false, 'primary_key' => ['id']]);
        $emails
            ->addColumn('id', 'uuid')
            ->addColumn('to_email', 'string', [
                'null' => false,
            ])
            ->addColumn('to_name', 'string', [
                'null' => true,
            ])
            ->addColumn('subject', 'text', [
                'null' => false,
            ])
            ->addColumn('message', 'text', [
                'null' => true,
            ])
            ->addColumn('metadata', 'text', [
                'null' => false,
                'limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG,
            ])
            ->addColumn('message_opened', 'boolean', [
                'null' => false,
                'default' => false,
            ])
            ->addColumn('opening_date', 'timestamp', [
                'null' => true,
                'timezone' => true,
            ])
            ->addColumn('created', 'timestamp', [
                'null' => true,
                'timezone' => true,
            ])
            ->addColumn('modified', 'timestamp', [
                'null' => true,
                'timezone' => true,
            ]);
        $emails->create();
    }
}
