<?php


use Phinx\Migration\AbstractMigration;

/**
 * Migration for adjusting the field length of the menu board product description/allergyInfo
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class MenuboardFieldLengthsMigration extends AbstractMigration
{
    public function change(): void
    {
        $this->table('menu_product')
            ->changeColumn('description', 'text', [
                'limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_REGULAR,
                'default' => null,
                'null' => true,
                ])
            ->changeColumn('allergyInfo', 'text', [
                'limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_REGULAR,
                'default' => null,
                'null' => true,
                ])
            ->save();
    }
}
