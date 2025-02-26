<?php


use Phinx\Migration\AbstractMigration;

/**
 * Minor DB changes for 3.2.0 - add logicalOperator columns to playlist and displayGroup tables
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class AddLogicalOperatorNameMigration extends AbstractMigration
{
    /** @inheritDoc */
    public function change()
    {
        $this->table('playlist')
            ->renameColumn('filterLogicalOperator', 'filterMediaTagsLogicalOperator')
            ->addColumn('filterMediaNameLogicalOperator', 'string', ['after' => 'filterMediaName', 'default' => 'OR', 'limit' => 3])
            ->save();

        $this->table('displaygroup')
            ->renameColumn('dynamicCriteriaLogicalOperator', 'dynamicCriteriaTagsLogicalOperator')
            ->addColumn('dynamicCriteriaLogicalOperator', 'string', ['after' => 'dynamicCriteria', 'default' => 'OR', 'limit' => 3])
            ->save();
    }
}
