<?php


use Phinx\Migration\AbstractMigration;

/**
 * Class PlaylistAddDynamicFilterMigration
 * add dynamic playlist filtering
 */
class PlaylistAddDynamicFilterMigration extends AbstractMigration
{
    /** @inheritdoc */
    public function change()
    {
        $table = $this->table('playlist');

        $this->execute('UPDATE `playlist` SET createdDt = \'1970-01-01 00:00:00\' WHERE createdDt < \'2000-01-01\'');
        $this->execute('UPDATE `playlist` SET modifiedDt = \'1970-01-01 00:00:00\' WHERE modifiedDt < \'2000-01-01\'');

        $table
            ->changeColumn('createdDt', 'datetime', ['null' => true, 'default' => null])
            ->changeColumn('modifiedDt', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('isDynamic', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'default' => 0])
            ->addColumn('filterMediaName', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('filterMediaTags', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->update();

        $task = $this->table('task');
        $task->insert([
                'name' => 'Sync Dynamic Playlists',
                'class' => '\Xibo\XTR\DynamicPlaylistSyncTask',
                'options' => '[]',
                'schedule' => '* * * * * *',
                'isActive' => '1',
                'configFile' => '/tasks/dynamic-playlist-sync.task'
            ])
            ->save();
    }
}
