<?php


use Phinx\Migration\AbstractMigration;

/**
 * Add a new column to connectors table (isVisible)
 * Add a new connector (layoutExchange) to connectors table
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class RegionTypeMigration extends AbstractMigration
{
    public function change()
    {
        $this->table('region')
            ->addColumn('type', 'string', [
                'null' => false,
                'default' => 'playlist',
                'limit' => 10
            ])
            ->save();
        
        // Update existing regions with the correct region type.
        $this->execute('
            UPDATE region SET type = \'frame\'
              WHERE regionId IN (
                SELECT regionId
                  FROM playlist
                    INNER JOIN widget
                    ON playlist.playlistId = widget.playlistId
                 WHERE IFNULL(regionId, 0) > 0
                GROUP BY regionId
                HAVING COUNT(widget.widgetId) = 1
              )
        ');
    }
}
