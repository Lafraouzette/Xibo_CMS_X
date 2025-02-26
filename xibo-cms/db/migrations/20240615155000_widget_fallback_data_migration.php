<?php


use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

/**
 * Migration to add a new table for modelling widget fallback data
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class WidgetFallbackDataMigration extends AbstractMigration
{
    public function change(): void
    {
        $this->table('widgetdata')
            ->addColumn('widgetId', 'integer', ['null' => false])
            ->addColumn('data', 'text', [
                'limit' => MysqlAdapter::TEXT_MEDIUM,
                'null' => true,
                'default' => null,
            ])
            ->addColumn('displayOrder', 'integer', [
                'limit' => MysqlAdapter::INT_MEDIUM,
            ])
            ->addColumn('createdDt', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modifiedDt', 'datetime', ['null' => true, 'default' => null])
            ->addForeignKey('widgetId', 'widget', 'widgetId')
            ->create();
    }
}
