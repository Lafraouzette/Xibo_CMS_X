<?php


use Phinx\Migration\AbstractMigration;

/**
 * Updated syncSwitchDelay and syncVideoPauseDelay columns to accept null values
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class UpdateSwitchDelayAndVideoPauseDelayMigration extends AbstractMigration
{
    public function change(): void
    {
        $this->table('syncgroup')
            ->changeColumn('syncSwitchDelay', 'integer', ['null' => true, 'default' => null])
            ->changeColumn('syncVideoPauseDelay', 'integer', ['null' => true, 'default' => null])
            ->update();
    }
}
