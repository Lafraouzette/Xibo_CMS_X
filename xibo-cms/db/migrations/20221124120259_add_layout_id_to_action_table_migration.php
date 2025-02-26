<?php


use Phinx\Migration\AbstractMigration;

/**
 * Add layoutId column to Action table
 * Populate layoutId for existing Actions
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class AddLayoutIdToActionTableMigration extends AbstractMigration
{
    /** @inheritDoc */
    public function change()
    {
        $this->table('action')
            ->addColumn('layoutId', 'integer', ['null' => true, 'default' => null])
            ->save();

        // Set layoutId to sourceId if the source is Layout
        $this->execute('UPDATE `action` SET layoutId = `action`.sourceId WHERE `action`.source = \'layout\' ');

        // Set layoutId to a layout corresponding with the regionId (sourceId) for region source
        foreach ($this->fetchAll('SELECT `region`.layoutId, `region`.regionId FROM `action` INNER JOIN `region` ON `action`.sourceId = `region`.regionId AND `action`.source = \'region\' ') as $regionAction) {
            $this->execute('UPDATE `action` SET `action`.layoutId =' . $regionAction['layoutId'] . ' WHERE `action`.sourceId = ' . $regionAction['regionId'] . ' AND `action`.source = \'region\' ');
        }

        // Set layoutId to Layout corresponding with widgetId (sourceId) for widget source
        foreach ($this->fetchAll('SELECT `region`.layoutId, `widget`.widgetId FROM `action` INNER JOIN `widget` ON `action`.sourceId = `widget`.widgetId AND `action`.source = \'widget\' INNER JOIN `playlist` ON `widget`.playlistId = `playlist`.playlistId INNER JOIN `region` ON `playlist`.regionId = `region`.regionId') as $widgetAction) {
            $this->execute('UPDATE `action` SET `action`.layoutId =' . $widgetAction['layoutId'] . ' WHERE `action`.sourceId = ' . $widgetAction['widgetId'] . ' AND `action`.source = \'widget\' ');
        }
    }
}
