<?php


use Phinx\Migration\AbstractMigration;

/**
 * Migration for adding more columns to displayevent table.
 * Add a new column on Command table for createAlertOn.
 * Add a new column on lkcommanddisplayprofile for createAlertOn.
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class DisplayAlertsMigration extends AbstractMigration
{
    public function change(): void
    {
        $this->table('displayevent')
            ->changeColumn('start', 'integer', ['null' => true])
            ->addColumn('eventTypeId', 'integer', ['null' => false, 'default' => 1])
            ->addColumn('refId', 'integer', ['null' => true, 'default' => null])
            ->addColumn('detail', 'text', ['null' => true, 'default' => null])
            ->save();

        $this->table('command')
            ->addColumn('createAlertOn', 'string', ['null' => false, 'default' => 'never'])
            ->save();

        $this->table('lkcommanddisplayprofile')
            ->addColumn('createAlertOn', 'string', ['null' => true, 'default' => null])
            ->save();
    }
}
