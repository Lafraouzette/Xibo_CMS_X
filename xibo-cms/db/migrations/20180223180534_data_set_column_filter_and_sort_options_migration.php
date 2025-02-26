<?php


use Phinx\Migration\AbstractMigration;

/**
 * Class DataSetColumnFilterAndSortOptionsMigration
 */
class DataSetColumnFilterAndSortOptionsMigration extends AbstractMigration
{
    /**
     * Add the show filter and show sort columns if they do not yet exist.
     */
    public function change()
    {
        $table = $this->table('datasetcolumn');

        if (!$table->hasColumn('showFilter')) {
            $table
                ->addColumn('showFilter', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'default' => 1])
                ->addColumn('showSort', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'default' => 1])
                ->update();
        }
    }
}
