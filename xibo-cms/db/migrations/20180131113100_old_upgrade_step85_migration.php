<?php


use Phinx\Migration\AbstractMigration;

class OldUpgradeStep85Migration extends AbstractMigration
{
    public function up()
    {
        $STEP = 85;

        // Are we an upgrade from an older version?
        if ($this->hasTable('version')) {
            // We do have a version table, so we're an upgrade from anything 1.7.0 onward.
            $row = $this->fetchRow('SELECT * FROM `version`');
            $dbVersion = $row['DBVersion'];

            // Are we on the relevent step for this upgrade?
            if ($dbVersion < $STEP) {
                $display = $this->table('display');

                if (!$display->hasColumn('storageAvailableSpace')) {
                    $display
                        ->addColumn('storageAvailableSpace', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_BIG, 'null' => true])
                        ->addColumn('storageTotalSpace', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_BIG, 'null' => true])
                        ->save();
                }

                // Bump our version
                $this->execute('UPDATE `version` SET DBVersion = ' . $STEP);
            }
        }
    }
}
