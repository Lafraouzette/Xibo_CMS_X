<?php


use Phinx\Migration\AbstractMigration;

/**
 * Add new notification type column
 * Add new notification email options for user groups
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class NotificationTypesMigration extends AbstractMigration
{
    public function change(): void
    {
        $this->table('notification')
            ->removeColumn('isEmail')
            ->addColumn('type', 'string', ['null' => false, 'default' => 'unknown', 'limit' => 50])
            ->save();

        $this->table('group')
            ->addColumn(
                'isDataSetNotification',
                'integer',
                ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'default' => 0]
            )
            ->addColumn(
                'isLayoutNotification',
                'integer',
                ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'default' => 0]
            )
            ->addColumn(
                'isLibraryNotification',
                'integer',
                ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'default' => 0]
            )
            ->addColumn(
                'isReportNotification',
                'integer',
                ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'default' => 0]
            )
            ->addColumn(
                'isScheduleNotification',
                'integer',
                ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'default' => 0]
            )
            ->addColumn(
                'isCustomNotification',
                'integer',
                ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'default' => 0]
            )
            ->save();
    }
}
