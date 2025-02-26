<?php


use Phinx\Migration\AbstractMigration;

/**
 * Migration for adding missing indexes
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class MissingIndexesMigration extends AbstractMigration
{
    public function change(): void
    {
        $region = $this->table('region');
        if (!$region->hasForeignKey('layoutId')) {
            // Take care of orphaned regions
            $this->execute('DELETE FROM `region` WHERE `layoutId` NOT IN (SELECT `layoutId` FROM `layout`)');

            // Add the FK
            $region
                ->addForeignKey('layoutId', 'layout', 'layoutId')
                ->save();
        }

        $playlist = $this->table('playlist');
        if (!$playlist->hasIndex('regionId')) {
            $playlist
                ->addIndex('regionId')
                ->save();
        }
    }
}
