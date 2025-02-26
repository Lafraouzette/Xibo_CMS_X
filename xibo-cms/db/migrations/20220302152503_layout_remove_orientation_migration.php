<?php


use Phinx\Migration\AbstractMigration;

/**
 * Remove weather tiles module to merge both weather widgets into one
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class LayoutRemoveOrientationMigration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function change()
    {
        $table = $this->table('layout');
        $table->removeColumn('orientation')->save();
    }
}
