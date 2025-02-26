<?php


use Phinx\Migration\AbstractMigration;

/**
 * Class LayoutPublishDraftMigration
 */
class LayoutPublishDraftMigration extends AbstractMigration
{
    /** @inheritdoc */
    public function change()
    {
        // Add a status table
        $status = $this->table('status');
        $status
            ->addColumn('status', 'string', ['limit' => 254])
            ->save();

        // We must ensure that the IDs are added as we expect (don't rely on auto_increment)
        $this->execute('INSERT INTO `status` (`id`, `status`) VALUES (1, \'Published\'), (2, \'Draft\'), (3, \'Pending Approval\')');

        $this->execute('UPDATE `layout` SET createdDt = \'1970-01-01 00:00:00\' WHERE createdDt < \'2000-01-01\'');
        $this->execute('UPDATE `layout` SET modifiedDt = \'1970-01-01 00:00:00\' WHERE modifiedDt < \'2000-01-01\'');

        // Add a reference to the Layout and Playlist tables for "parentId"
        $layout = $this->table('layout');
        $layout
            ->changeColumn('createdDt', 'datetime', ['null' => true, 'default' => null])
            ->changeColumn('modifiedDt', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('parentId', 'integer', ['default' => null, 'null' => true])
            ->addColumn('publishedStatusId', 'integer', ['default' => 1])
            ->addForeignKey('publishedStatusId', 'status')
            ->save();
    }
}
