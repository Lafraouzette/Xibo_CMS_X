<?php


use Phinx\Migration\AbstractMigration;

/**
 * Add the world clock module if it doesn't already exist
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class AddWorldClockMigration extends AbstractMigration
{
    /** @inheritDoc */
    public function change()
    {
        if (!$this->fetchRow('SELECT * FROM module WHERE module = \'worldclock\'')) {
            $modules = $this->table('module');
            $modules->insert([
                'module' => 'worldclock',
                'name' => 'World Clock',
                'enabled' => 1,
                'regionSpecific' => 1,
                'description' => 'WorldClock Module',
                'schemaVersion' => 1,
                'previewEnabled' => 1,
                'assignable' => 1,
                'render_as' => 'html',
                'viewPath' => '../modules',
                'class' => 'Xibo\Widget\WorldClock',
                'defaultDuration' => 60,
                'installName' => 'worldclock'
            ])->save();
        }
    }
}
