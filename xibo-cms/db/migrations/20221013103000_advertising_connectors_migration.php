<?php


use Phinx\Migration\AbstractMigration;

/**
 * Minor DB changes for 3.2.0
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class AdvertisingConnectorsMigration extends AbstractMigration
{
    /** @inheritDoc */
    public function change()
    {
        $this->table('connectors')
            ->insert([
                [
                    'className' => '\\Xibo\\Connector\\XiboSspConnector',
                    'isEnabled' => 0,
                    'isVisible' => 1
                ],
                [
                    'className' => '\\Xibo\\Connector\\XiboAudienceReportingConnector',
                    'isEnabled' => 0,
                    'isVisible' => 1
                ],
            ])
            ->save();
    }
}
