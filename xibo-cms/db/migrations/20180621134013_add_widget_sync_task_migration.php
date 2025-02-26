<?php


use Phinx\Migration\AbstractMigration;

/**
 * Class AddWidgetSyncTaskMigration
 */
class AddWidgetSyncTaskMigration extends AbstractMigration
{
    /** @inheritdoc */
    public function change()
    {
        // Check to see if the mail_from_name setting exists
        if (!$this->fetchRow('SELECT * FROM `task` WHERE name = \'Widget Sync\'')) {
            $this->execute('INSERT INTO `task` SET `name`=\'Widget Sync\', `class`=\'\\\\Xibo\\\\XTR\\\\WidgetSyncTask\', `status`=2, `isActive`=1, `configFile`=\'/tasks/widget-sync.task\', `options`=\'{}\', `schedule`=\'*/3 * * * *\';');
        }
    }
}
