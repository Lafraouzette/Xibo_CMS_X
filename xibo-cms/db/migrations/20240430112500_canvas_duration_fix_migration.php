<?php


use Phinx\Migration\AbstractMigration;

/**
 * Migration to fix the Canvas duration which mistakenly got added as 60s instead of 1s
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class CanvasDurationFixMigration extends AbstractMigration
{
    public function change(): void
    {
        $this->execute('UPDATE `module` SET `defaultDuration` = 1, `enabled` = 1 WHERE `moduleId` = :moduleId', [
            'moduleId' => 'core-canvas',
        ]);
    }
}
