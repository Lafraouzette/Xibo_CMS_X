<?php


use Phinx\Migration\AbstractMigration;

/**
 * Class DisplayAsVncLinkMigration
 */
class DisplayAsVncLinkMigration extends AbstractMigration
{
    /** @inheritdoc */
    public function change()
    {
        $this->query('UPDATE `setting` SET title = \'Add a link to the Display name using this format mask?\', helpText = \'Turn the display name in display management into a link using the IP address last collected. The %s is replaced with the IP address. Leave blank to disable.\' WHERE setting = \'SHOW_DISPLAY_AS_VNCLINK\';');

        $this->query('UPDATE `setting` SET title = \'The target attribute for the above link\' WHERE setting = \'SHOW_DISPLAY_AS_VNC_TGT\';');
    }
}
