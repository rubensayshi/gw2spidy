<?php

namespace GW2Spidy\DB\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'item' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.gw2spidy.map
 */
class ItemTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'gw2spidy.map.ItemTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('item');
        $this->setPhpName('Item');
        $this->setClassname('GW2Spidy\\DB\\Item');
        $this->setPackage('gw2spidy');
        $this->setUseIdGenerator(false);
        // columns
        $this->addPrimaryKey('DATA_ID', 'DataId', 'INTEGER', true, null, null);
        $this->addForeignKey('TYPE_ID', 'TypeId', 'INTEGER', 'item_type', 'ID', true, null, null);
        $this->addColumn('NAME', 'Name', 'VARCHAR', true, 255, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('ItemType', '\\GW2Spidy\\DB\\ItemType', RelationMap::MANY_TO_ONE, array('type_id' => 'id', ), null, null);
    } // buildRelations()

} // ItemTableMap
