<?php


use Phinx\Migration\AbstractMigration;

/**
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class AddDefaultChromeOSDisplayProfileMigration extends AbstractMigration
{
    public function change(): void
    {
        // add default display profile for tizen
        if (!$this->fetchRow('SELECT * FROM displayprofile WHERE type = \'chromeOS\' AND isDefault = 1')) {
            // Get system user
            $user = $this->fetchRow('SELECT userId FROM `user` WHERE userTypeId = 1');

            $this->table('displayprofile')->insert([
                'name' => 'ChromeOS',
                'type' => 'chromeOS',
                'config' => '[]',
                'userId' => $user['userId'],
                'isDefault' => 1
            ])->save();
        }
    }
}
