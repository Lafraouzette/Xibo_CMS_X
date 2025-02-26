<?php


use Phinx\Migration\AbstractMigration;

/**
 * Remove weather tiles module to merge both weather widgets into one
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class MergeWeatherWidgets extends AbstractMigration
{
    public function change()
    {
        // Delete weather tiles table
        $this->execute('DELETE FROM `module` WHERE module = \'weather\' ');
    }
}
