<?php

namespace GW2Spidy\DB\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'buy_listing' table.
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
class BuyListingTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'gw2spidy.map.BuyListingTableMap';

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
        $this->setName('buy_listing');
        $this->setPhpName('BuyListing');
        $this->setClassname('GW2Spidy\\DB\\BuyListing');
        $this->setPackage('gw2spidy');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('LISTING_DATE', 'ListingDate', 'DATE', true, null, null);
        $this->addColumn('LISTING_TIME', 'ListingTime', 'TIME', true, null, null);
        $this->addColumn('LISTING_DATETIME', 'ListingDatetime', 'TIMESTAMP', true, null, null);
        $this->addForeignKey('ITEM_ID', 'ItemId', 'INTEGER', 'item', 'DATA_ID', true, null, null);
        $this->addColumn('LISTINGS', 'Listings', 'INTEGER', true, null, null);
        $this->addColumn('UNIT_PRICE', 'UnitPrice', 'INTEGER', true, null, null);
        $this->addColumn('QUANTITY', 'Quantity', 'INTEGER', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Item', 'GW2Spidy\\DB\\Item', RelationMap::MANY_TO_ONE, array('item_id' => 'data_id', ), null, null);
    } // buildRelations()

} // BuyListingTableMap
