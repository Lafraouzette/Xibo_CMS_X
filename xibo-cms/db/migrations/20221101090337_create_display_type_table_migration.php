<?php


use Phinx\Migration\AbstractMigration;

/**
 * Add display types
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class CreateDisplayTypeTableMigration extends AbstractMigration
{
    /** @inheritDoc */
    public function change()
    {
        $displayType = $this->table('display_types', ['id' => 'displayTypeId']);
        $displayType->addColumn('displayType', 'string', ['limit' => 100])
            ->insert([
                ['displayTypeId' => 1, 'displayType' => 'Billboard'],
                ['displayTypeId' => 2, 'displayType' => 'Kiosk'],
                ['displayTypeId' => 3, 'displayType' => 'LED Matrix / LED Video Wall'],
                ['displayTypeId' => 4, 'displayType' => 'Monitor / Other'],
                ['displayTypeId' => 5, 'displayType' => 'Projector'],
                ['displayTypeId' => 6, 'displayType' => 'Shelf-edge Display'],
                ['displayTypeId' => 7, 'displayType' => 'Smart Mirror'],
                ['displayTypeId' => 8, 'displayType' => 'TV / Panel'],
                ['displayTypeId' => 9, 'displayType' => 'Tablet'],
                ['displayTypeId' => 10, 'displayType' => 'Totem'],
            ])
            ->save();
    }
}
