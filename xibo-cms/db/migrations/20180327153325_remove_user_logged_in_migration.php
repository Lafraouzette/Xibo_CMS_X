<?php


use Phinx\Migration\AbstractMigration;

/**
 * Class RemoveUserLoggedInMigration
 * Removes the logged in column if it still exists.
 */
class RemoveUserLoggedInMigration extends AbstractMigration
{
    /** @inheritdoc */
    public function change()
    {
        $table = $this->table('user');
        if ($table->hasColumn('loggedIn')) {
            $table->removeColumn('loggedIn')->update();
        }
    }
}
