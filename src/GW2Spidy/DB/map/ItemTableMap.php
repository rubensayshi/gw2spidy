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
        $this->addColumn('TYPE_ID', 'TypeId', 'INTEGER', true, null, null);
        $this->addColumn('NAME', 'Name', 'VARCHAR', true, 255, null);
        $this->addColumn('GEM_STORE_DESCRIPTION', 'GemStoreDescription', 'VARCHAR', true, 255, null);
        $this->addColumn('GEM_STORE_BLURB', 'GemStoreBlurb', 'VARCHAR', true, 255, null);
        $this->addColumn('RESTRICTION_LEVEL', 'RestrictionLevel', 'VARCHAR', true, 255, null);
        $this->addColumn('RARITY', 'Rarity', 'VARCHAR', true, 255, null);
        $this->addColumn('VENDOR_SELL_PRICE', 'VendorSellPrice', 'VARCHAR', true, 255, null);
        $this->addColumn('IMG', 'Img', 'VARCHAR', true, 255, null);
        $this->addColumn('RARITY_WORD', 'RarityWord', 'VARCHAR', true, 255, null);
        $this->addForeignKey('ITEM_TYPE_ID', 'ItemTypeId', 'INTEGER', 'item_type', 'ID', true, null, null);
        $this->addForeignKey('ITEM_SUB_TYPE_ID', 'ItemSubTypeId', 'INTEGER', 'item_sub_type', 'ID', true, null, null);
        $this->addColumn('MAX_OFFER_UNIT_PRICE', 'MaxOfferUnitPrice', 'INTEGER', true, null, null);
        $this->addColumn('MIN_SALE_UNIT_PRICE', 'MinSaleUnitPrice', 'INTEGER', true, null, null);
        $this->addColumn('OFFER_AVAILABILITY', 'OfferAvailability', 'INTEGER', true, null, 0);
        $this->addColumn('SALE_AVAILABILITY', 'SaleAvailability', 'INTEGER', true, null, 0);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('ItemType', 'GW2Spidy\\DB\\ItemType', RelationMap::MANY_TO_ONE, array('item_type_id' => 'id', ), null, null);
        $this->addRelation('ItemSubType', 'GW2Spidy\\DB\\ItemSubType', RelationMap::MANY_TO_ONE, array('item_sub_type_id' => 'id', ), null, null);
        $this->addRelation('SellListing', 'GW2Spidy\\DB\\SellListing', RelationMap::ONE_TO_MANY, array('data_id' => 'item_id', ), null, null, 'SellListings');
        $this->addRelation('BuyListing', 'GW2Spidy\\DB\\BuyListing', RelationMap::ONE_TO_MANY, array('data_id' => 'item_id', ), null, null, 'BuyListings');
    } // buildRelations()

} // ItemTableMap
