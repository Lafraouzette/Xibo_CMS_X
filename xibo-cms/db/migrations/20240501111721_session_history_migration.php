<?php


use Phinx\Migration\AbstractMigration;

/**
 * Migration to Add a new table for session_history
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class SessionHistoryMigration extends AbstractMigration
{
    public function change(): void
    {
        $this->table('session_history', ['id' => 'sessionId'])
            ->addColumn('ipAddress', 'string', ['limit' => 50, 'null' => true, 'default' => null])
            ->addColumn('userAgent', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('startTime', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('userId', 'integer', ['null' => true, 'default' => null])
            ->addIndex('userId')
            ->create();

        $this->table('auditlog')
            ->addColumn('sessionHistoryId', 'integer', ['null' => true, 'default' => null])
            ->save();

        $this->table('log')
            ->addColumn('sessionHistoryId', 'integer', ['null' => true, 'default' => null])
            ->save();
    }
}
