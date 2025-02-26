<?php


use Phinx\Migration\AbstractMigration;

/**
 * Add display venue metadata
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */

class CreateDisplayLocationTypeTableMigration extends AbstractMigration
{
    /** @inheritDoc */
    public function change()
    {
        $this->table('display')
            ->addColumn('venueId', 'integer', ['after' => 'screenSize', 'default' => null, 'null' => true])
            ->addColumn('isMobile', 'integer', ['after' => 'screenSize', 'limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'default' => 0])
            ->addColumn('address', 'text', ['after' => 'screenSize', 'default' => null, 'null' => true])
            ->addColumn('languages', 'text', ['after' => 'screenSize', 'default' => null, 'null' => true])
            ->save();
    }
}
