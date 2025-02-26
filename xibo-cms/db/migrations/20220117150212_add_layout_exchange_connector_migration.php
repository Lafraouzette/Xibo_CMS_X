<?php


use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

/**
 * Add a new column to connectors table (isVisible)
 * Add a new connector (layoutExchange) to connectors table
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class AddLayoutExchangeConnectorMigration extends AbstractMigration
{
    public function change()
    {
        $this->table('connectors')
            ->addColumn('isVisible', 'integer', [
                'default' => 1,
                'null' => false,
                'limit' => MysqlAdapter::INT_TINY
            ])
            ->insert([
                'className' => '\\Xibo\\Connector\\XiboExchangeConnector',
                'isEnabled' => 0,
                'isVisible' => 1
            ])
            ->save();
    }
}
