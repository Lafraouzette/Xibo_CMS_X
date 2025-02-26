<?php


use Phinx\Migration\AbstractMigration;

/**
 * Class FixCaseOnHelpTextFieldMigration
 */
class FixCaseOnHelpTextFieldMigration extends AbstractMigration
{
    /** @inheritdoc */
    public function change()
    {
        $table = $this->table('setting');
        if ($table->hasColumn('helptext')) {
            $table->renameColumn('helptext', 'helpText')->save();
        }
    }
}
