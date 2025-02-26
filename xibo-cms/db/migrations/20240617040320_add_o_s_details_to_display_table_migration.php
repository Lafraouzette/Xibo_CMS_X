<?php


use Phinx\Migration\AbstractMigration;

/**
 * Migration to Add osVersion, osSdk, manufacturer, brand, model columns to Display table
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class AddOSDetailsToDisplayTableMigration extends AbstractMigration
{
    public function change(): void
    {
        $this->table('display')
            ->addColumn('osVersion', 'string', ['default' => null, 'null' => true])
            ->addColumn('osSdk', 'string', ['default' => null, 'null' => true])
            ->addColumn('manufacturer', 'string', ['default' => null, 'null' => true])
            ->addColumn('brand', 'string', ['default' => null, 'null' => true])
            ->addColumn('model', 'string', ['default' => null, 'null' => true])
            ->save();
    }
}
