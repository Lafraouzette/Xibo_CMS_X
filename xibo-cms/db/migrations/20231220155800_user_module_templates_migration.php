<?php


use Phinx\Migration\AbstractMigration;

/**
 * Migrations for adding user supplied module templates
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class UserModuleTemplatesMigration extends AbstractMigration
{
    public function change(): void
    {
        $this->table('module_templates')
            ->addColumn('templateId', 'string', [
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('dataType', 'string', [
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('xml', 'text', [
                'limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_MEDIUM,
                'null' => false,
            ])
            ->addColumn('enabled', 'integer', [
                'limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,
                'null' => false,
                'default' => 1,
            ])
            ->addColumn('ownerId', 'integer')
            ->addForeignKey('ownerId', 'user', 'userId')
            ->save();

        $this->table('permissionentity')
            ->insert([
                ['entity' => 'Xibo\Entity\ModuleTemplate']
            ])
            ->save();
    }
}
