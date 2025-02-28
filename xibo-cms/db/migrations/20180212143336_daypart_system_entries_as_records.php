<?php


use Phinx\Migration\AbstractMigration;

/**
 * Class DaypartSystemEntriesAsRecords
 */
class DaypartSystemEntriesAsRecords extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $dayPart = $this->table('daypart');

        if (!$dayPart->hasColumn('isAlways')) {
            $dayPart
                ->addColumn('isAlways', 'integer', ['default' => 0, 'limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY])
                ->addColumn('isCustom', 'integer', ['default' => 0, 'limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY])
                ->insert([
                    [
                        'name' => 'Custom',
                        'description' => 'User specifies the from/to date',
                        'isRetired' => 0,
                        'userid' => 1,
                        'startTime' => '',
                        'endTime' => '',
                        'exceptions' => '',
                        'isAlways' => 0,
                        'isCustom' => 1
                    ], [
                        'name' => 'Always',
                        'description' => 'Event runs always',
                        'isRetired' => 0,
                        'userid' => 1,
                        'startTime' => '',
                        'endTime' => '',
                        'exceptions' => '',
                        'isAlways' => 1,
                        'isCustom' => 0
                    ]
                ])
                ->save();

            // Execute some SQL to bring the existing records into line.
            $this->execute('UPDATE `schedule` SET dayPartId = (SELECT dayPartId FROM daypart WHERE isAlways = 1) WHERE dayPartId = 1');
            $this->execute('UPDATE `schedule` SET dayPartId = (SELECT dayPartId FROM daypart WHERE isCustom = 1) WHERE dayPartId = 0');

            // Add some default permissions
            $this->execute('
                INSERT INTO `permission` (entityId, groupId, objectId, view, edit, `delete`)
                  SELECT entityId, groupId, dayPartId, 1, 0, 0
                   FROM daypart
                    CROSS JOIN permissionentity
                    CROSS JOIN `group`
                  WHERE entity LIKE \'%DayPart\' AND IsEveryone = 1 AND (isCustom = 1 OR isAlways = 1);
            ');
        }
    }
}
