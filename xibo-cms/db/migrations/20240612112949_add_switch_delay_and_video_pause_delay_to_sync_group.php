<?php


use Phinx\Migration\AbstractMigration;

/**
 * Added syncSwitchDelay and syncVideoPauseDelay columns to Sync Groups
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class AddSwitchDelayAndVideoPauseDelayToSyncGroup extends AbstractMigration
{
    public function change(): void
    {
        $this->table('syncgroup')
            ->addColumn('syncSwitchDelay', 'integer', ['null' => false, 'default' => 1])
            ->addColumn('syncVideoPauseDelay', 'integer', ['null' => true, 'default' => null])
            ->update();
    }
}
