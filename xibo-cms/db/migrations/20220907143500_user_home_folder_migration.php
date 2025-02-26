<?php


use Phinx\Migration\AbstractMigration;

/**
 * Add home folder to users.
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class UserHomeFolderMigration extends AbstractMigration
{
    /** @inheritDoc */
    public function change()
    {
        // Add a new field for the home folder
        $this->table('user')
            ->addColumn('homeFolderId', 'integer', [
                'limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_REGULAR,
                'null' => false,
                'default' => 1,
            ])
            ->addForeignKey('homeFolderId', 'folder', 'folderId')
            ->save();
    }
}
