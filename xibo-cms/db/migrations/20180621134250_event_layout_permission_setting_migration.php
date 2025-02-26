<?php


use Phinx\Migration\AbstractMigration;

/**
 * Class EventLayoutPermissionSettingMigration
 */
class EventLayoutPermissionSettingMigration extends AbstractMigration
{
    /** @inheritdoc */
    public function up()
    {
        // Check to see if the mail_from_name setting exists
        if (!$this->fetchRow('SELECT * FROM `setting` WHERE setting = \'SCHEDULE_SHOW_LAYOUT_NAME\'')) {
            $this->execute('INSERT INTO `setting` (`setting`, `value`, `fieldType`, `helptext`, `options`, `cat`, `userChange`, `title`, `validation`, `ordering`, `default`, `userSee`, `type`) VALUES (\'SCHEDULE_SHOW_LAYOUT_NAME\', \'0\', \'checkbox\', \'If checked then the Schedule will show the Layout for existing events even if the logged in User does not have permission to see that Layout.\', null, \'permissions\', 1, \'Show event Layout regardless of User permission?\', \'\', 45, \'\', 1, \'checkbox\');');
        }
    }
}
