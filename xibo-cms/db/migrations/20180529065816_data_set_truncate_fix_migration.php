<?php


use Phinx\Migration\AbstractMigration;

/**
 * Class DataSetTruncateFixMigration
 */
class DataSetTruncateFixMigration extends AbstractMigration
{
    /** @inheritdoc */
    public function change()
    {
        $table = $this->table('dataset');

        if (!$table->hasColumn('lastClear')) {
            $table
                ->addColumn('lastClear', 'integer')
                ->changeColumn('authentication', 'string', ['limit' => 10, 'default' => null, 'null' => true])
                ->save();
        }
    }
}
