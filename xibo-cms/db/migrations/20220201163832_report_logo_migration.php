<?php


use Phinx\Migration\AbstractMigration;

/**
 * Report Logo Migration
 * ---------------------
 * Add a setting for whether to show the logo on PDF exports
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class ReportLogoMigration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function change()
    {
        $this->table('setting')
            ->insert([
                'setting' => 'REPORTS_EXPORT_SHOW_LOGO',
                'value' => 1,
                'userSee' => 1,
                'userChange' => 1
            ])
            ->save();
    }
}
