<?php


use Phinx\Migration\AbstractMigration;

/**
 * Add a new column (code) to menu_board, menu_category and menu_product tables
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class AddCodeColumnToMenuBoardsMigration extends AbstractMigration
{
    public function change()
    {
        $this->table('menu_board')
            ->addColumn('code', 'string', ['limit' => 50, 'after' => 'description', 'null' => true, 'default' => null])
            ->save();

        $this->table('menu_category')
            ->addColumn('code', 'string', ['limit' => 50, 'null' => true, 'default' => null])
            ->save();

        $this->table('menu_product')
            ->addColumn('code', 'string', ['limit' => 50, 'null' => true, 'default' => null])
            ->save();
    }
}
