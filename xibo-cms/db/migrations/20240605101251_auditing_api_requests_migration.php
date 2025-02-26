<?php


use Phinx\Migration\AbstractMigration;

/**
 * Migration to Add a new table for session_history
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class AuditingApiRequestsMigration extends AbstractMigration
{
    public function change(): void
    {
        $this->table('application_requests_history', ['id' => 'requestId'])
            ->addColumn('userId', 'integer', ['null' => true, 'default' => null])
            ->addColumn('applicationId', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('url', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('method', 'string', ['limit' => 20, 'null' => true, 'default' => null])
            ->addColumn('startTime', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('endTime', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('duration', 'integer', ['null' => true, 'default' => null])
            ->addIndex('userId')
            ->create();

        $this->table('auditlog')
            ->addColumn('requestId', 'integer', ['null' => true, 'default' => null])
            ->save();

        $this->table('log')
            ->addColumn('requestId', 'integer', ['null' => true, 'default' => null])
            ->save();
    }
}
