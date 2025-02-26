<?php


use Phinx\Migration\AbstractMigration;

/**
 * Class WidgetCreatedAndModifiedDtMigration
 */
class WidgetCreatedAndModifiedDtMigration extends AbstractMigration
{
    /**
     *
     */
    public function change()
    {
        $table = $this->table('widget');

        if (!$table->hasColumn('modifiedDt')) {

            $table
                ->addColumn('createdDt', 'integer', ['default' => 0])
                ->addColumn('modifiedDt', 'integer', ['default' => 0])
                ->update();
        }
    }
}
