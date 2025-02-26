<?php


use Phinx\Migration\AbstractMigration;

/**
 * Class CreateStatTableMigration
 */
class CreateStatTableMigration extends AbstractMigration
{
    /** @inheritdoc */
    public function change()
    {
        // If stat table exists then rename it
        if ($this->hasTable('stat')) {
            $this->table('stat')->rename('stat_archive')->save();
        }

        // Create stat table
        $table = $this->table('stat', ['id' => 'statId']);
        $table

            ->addColumn('type', 'string', ['limit' => 20])
            ->addColumn('statDate', 'integer')
            ->addColumn('scheduleId', 'integer')
            ->addColumn('displayId', 'integer')
            ->addColumn('campaignId', 'integer', ['default' => null, 'null' => true])
            ->addColumn('layoutId', 'integer', ['default' => null, 'null' => true])
            ->addColumn('mediaId', 'integer', ['default' => null, 'null' => true])
            ->addColumn('widgetId', 'integer', ['default' => null, 'null' => true])
            ->addColumn('start', 'integer')
            ->addColumn('end', 'integer')
            ->addColumn('tag', 'string', ['limit' => 254, 'default' => null, 'null' => true])
            ->addColumn('duration', 'integer')
            ->addColumn('count', 'integer')

            ->addIndex('statDate')
            ->addIndex(['displayId', 'end', 'type'])
            ->save();


    }
}