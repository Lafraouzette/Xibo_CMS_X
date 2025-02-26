<?php


use Phinx\Migration\AbstractMigration;

/**
 * Add display metadata in display and display group
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class AddDisplayMetaDataMigration extends AbstractMigration
{
    /** @inheritDoc */
    public function change()
    {
        $this->table('display')
            ->addColumn('screenSize', 'integer', ['after' => 'display', 'default' => null, 'null' => true])
            ->addColumn('displayTypeId', 'integer', ['after' => 'display', 'default' => null, 'null' => true])
            ->addColumn('impressionsPerPlay', 'decimal', ['precision' => 10, 'scale' => 4, 'after' => 'lanIpAddress', 'default' => null, 'null' => true])
            ->addColumn('costPerPlay', 'decimal', ['precision' => 10, 'scale' => 4, 'after' => 'lanIpAddress', 'default' => null, 'null' => true])
            ->addColumn('isOutdoor', 'integer', ['after' => 'lanIpAddress', 'limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'default' => 0])
            ->addColumn('customId', 'string', ['after' => 'lanIpAddress', 'limit' => 254, 'default' => null, 'null' => true])
            ->addForeignKey('displayTypeId', 'display_types', 'displayTypeId')
            ->save();

        $this->table('displaygroup')
            ->addColumn('ref5', 'text', ['after' => 'bandwidthLimit', 'default' => null, 'null' => true])
            ->addColumn('ref4', 'text', ['after' => 'bandwidthLimit', 'default' => null, 'null' => true])
            ->addColumn('ref3', 'text', ['after' => 'bandwidthLimit', 'default' => null, 'null' => true])
            ->addColumn('ref2', 'text', ['after' => 'bandwidthLimit', 'default' => null, 'null' => true])
            ->addColumn('ref1', 'text', ['after' => 'bandwidthLimit', 'default' => null, 'null' => true])
            ->save();
    }
}
