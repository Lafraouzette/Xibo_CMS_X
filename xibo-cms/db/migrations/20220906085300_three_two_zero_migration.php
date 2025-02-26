<?php


use Phinx\Migration\AbstractMigration;

/**
 * Minor DB changes for 3.2.0
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class ThreeTwoZeroMigration extends AbstractMigration
{
    /** @inheritDoc */
    public function change()
    {
        // New Ip Address field
        $this->table('display')
            ->addColumn('lanIpAddress', 'string', [
                'limit' => 50,
                'null' => true,
                'default' => null,
            ])
            ->save();

        // Add the Dashboards connector, disabled.
        $this->table('connectors')
            ->insert([
                'className' => '\\Xibo\\Connector\\XiboDashboardConnector',
                'isEnabled' => 0,
                'isVisible' => 1
            ])
            ->save();

        // Dynamic criteria tags
        $this->table('displaygroup')
            ->changeColumn('dynamicCriteria', 'text', [
                'null' => true,
                'default' => null,
            ])
            ->save();

        $this->table('playlist')
            ->changeColumn('filterMediaName', 'text', [
                'null' => true,
                'default' => null
            ])
            ->changeColumn('filterMediaTags', 'text', [
                'null' => true,
                'default' => null
            ])
            ->save();

        // Resolution on media
        $this->table('media')
            ->addColumn('width', 'integer', [
                'limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_MEDIUM,
                'null' => true,
                'default' => null,
            ])
            ->addColumn('height', 'integer', [
                'limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_MEDIUM,
                'null' => true,
                'default' => null,
            ])
            ->save();

        // Setting for folders.
        if (!$this->fetchRow('SELECT * FROM `setting` WHERE setting = \'FOLDERS_ALLOW_SAVE_IN_ROOT\'')) {
            $this->table('setting')->insert([
                [
                    'setting' => 'FOLDERS_ALLOW_SAVE_IN_ROOT',
                    'value' => '1',
                    'userSee' => 1,
                    'userChange' => 1
                ]
            ])->save();
        }
    }
}
