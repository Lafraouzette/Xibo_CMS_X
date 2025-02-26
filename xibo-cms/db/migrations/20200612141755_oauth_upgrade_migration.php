<?php


use Phinx\Migration\AbstractMigration;

/**
 * Class OauthUpgradeMigration
 */
class OauthUpgradeMigration extends AbstractMigration
{
    /** @inheritDoc */
    public function change()
    {
        // Delete oAuth tables which are no longer in use.
        $this->table('oauth_access_token_scopes')->drop()->save();
        $this->table('oauth_session_scopes')->drop()->save();
        $this->table('oauth_refresh_tokens')->drop()->save();
        $this->table('oauth_auth_code_scopes')->drop()->save();
        $this->table('oauth_auth_codes')->drop()->save();
        $this->table('oauth_access_tokens')->drop()->save();
        $this->table('oauth_sessions')->drop()->save();

        // Add a new column to the Applications table to indicate whether an app is confidential or not
        $clients = $this->table('oauth_clients');
        if (!$clients->hasColumn('isConfidential')) {
            $clients
                ->addColumn('isConfidential', 'integer', [
                    'default' => 1,
                    'limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY
                ])
                ->save();
        }
    }
}
