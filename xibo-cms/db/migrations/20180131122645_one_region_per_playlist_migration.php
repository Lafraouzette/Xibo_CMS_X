<?php


use Phinx\Migration\AbstractMigration;

/**
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 * @phpcs:disable Generic.Files.LineLength.TooLong
 */
class OneRegionPerPlaylistMigration extends AbstractMigration
{
    /**
     * Up
     */
    public function up()
    {
        $playlist = $this->table('playlist');
        $playlist
            ->addColumn('regionId', 'integer', ['null' => true])
            ->addColumn('createdDt', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modifiedDt', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('duration', 'integer', ['default' => 0])
            ->addColumn(
                'requiresDurationUpdate',
                'integer',
                ['default' => 0, 'limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY]
            )
            ->addIndex('regionId')
            ->save();

        $this->execute('UPDATE `playlist` SET regionId = (SELECT MAX(regionId) FROM lkregionplaylist WHERE playlist.playlistId = lkregionplaylist.playlistId);');

        $this->table('lkregionplaylist')->drop()->save();

        $this->execute('UPDATE `pages` SET asHome = 1 WHERE name = \'playlist\';');

        $this->execute('
          INSERT INTO module (Module, Name, Enabled, RegionSpecific, Description, ImageUri, SchemaVersion, ValidExtensions, PreviewEnabled, assignable, render_as, settings, viewPath, class, defaultDuration) 
          VALUES (\'subplaylist\', \'Sub-Playlist\', 1, 1, \'Embed a Sub-Playlist\', \'forms/library.gif\', 1, null, 1, 1, \'native\', null, \'../modules\', \'Xibo\\\\Widget\\\\SubPlaylist\', 10);
        ');

        $playlistClosure = $this->table('lkplaylistplaylist', ['id' => false, 'primary_key' => ['parentId', 'childId', 'depth']]);
        $playlistClosure
            ->addColumn('parentId', 'integer')
            ->addColumn('childId', 'integer')
            ->addColumn('depth', 'integer')
            ->addIndex(['childId', 'parentId', 'depth'], ['unique' => true])
            ->save();

        $this->execute('INSERT INTO lkplaylistplaylist (parentId, childId, depth) SELECT playlistId, playlistId, 0 FROM playlist;');
    }
}
