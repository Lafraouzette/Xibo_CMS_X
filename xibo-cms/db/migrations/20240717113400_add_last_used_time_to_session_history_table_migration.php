<?php


use Phinx\Migration\AbstractMigration;

/**
 * Migration to add lastUsedTime in session_history table
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class AddLastUsedTimeToSessionHistoryTableMigration extends AbstractMigration
{
    public function change(): void
    {
        $this->table('session_history')
            ->addColumn('lastUsedTime', 'datetime', ['null' => true, 'default' => null])
            ->save();
    }
}
