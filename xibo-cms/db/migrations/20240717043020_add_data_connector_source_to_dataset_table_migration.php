<?php


use Phinx\Migration\AbstractMigration;

/**
 * Migration to add data source column to the dataset table.
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class AddDataConnectorSourceToDatasetTableMigration extends AbstractMigration
{
    public function change(): void
    {
        $this->table('dataset')
            ->addColumn('dataConnectorSource', 'string', ['default' => null, 'null' => true])
            ->save();
    }
}
