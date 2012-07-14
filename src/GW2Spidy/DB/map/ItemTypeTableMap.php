<?php

namespace \GW2Spidy\DB\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'item_type' table.
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
class ItemTypeTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'gw2spidy.map.ItemTypeTableMap';

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
        $this->setName('item_type');
        $this->setPhpName('ItemType');
        $this->setClassname('\\GW2Spidy\\DB\\ItemType');
        $this->setPackage('gw2spidy');
        $this->setUseIdGenerator(false);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('TITLE', 'Title', 'VARCHAR', true, 255, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Item', 'GW2Spidy\\DB\\Item', RelationMap::ONE_TO_MANY, array('id' => 'type_id', ), null, null, 'Items');
    } // buildRelations()

} // ItemTypeTableMap
