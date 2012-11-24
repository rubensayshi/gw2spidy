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
        $this->addColumn('RESTRICTION_LEVEL', 'RestrictionLevel', 'INTEGER', true, null, null);
        $this->addColumn('RARITY', 'Rarity', 'INTEGER', true, null, null);
        $this->addColumn('VENDOR_SELL_PRICE', 'VendorSellPrice', 'INTEGER', true, null, null);
        $this->addColumn('VENDOR_PRICE', 'VendorPrice', 'INTEGER', true, null, null);
        $this->addColumn('KARMA_PRICE', 'KarmaPrice', 'INTEGER', true, null, null);
        $this->addColumn('IMG', 'Img', 'VARCHAR', true, 255, null);
        $this->addColumn('RARITY_WORD', 'RarityWord', 'VARCHAR', true, 255, null);
        $this->addForeignKey('ITEM_TYPE_ID', 'ItemTypeId', 'INTEGER', 'item_type', 'ID', false, null, null);
        $this->addForeignKey('ITEM_SUB_TYPE_ID', 'ItemSubTypeId', 'INTEGER', 'item_sub_type', 'ID', false, null, null);
        $this->addColumn('MAX_OFFER_UNIT_PRICE', 'MaxOfferUnitPrice', 'INTEGER', true, null, null);
        $this->addColumn('MIN_SALE_UNIT_PRICE', 'MinSaleUnitPrice', 'INTEGER', true, null, null);
        $this->addColumn('OFFER_AVAILABILITY', 'OfferAvailability', 'INTEGER', true, null, 0);
        $this->addColumn('SALE_AVAILABILITY', 'SaleAvailability', 'INTEGER', true, null, 0);
        $this->addColumn('GW2DB_ID', 'Gw2dbId', 'INTEGER', false, null, null);
        $this->addColumn('GW2DB_EXTERNAL_ID', 'Gw2dbExternalId', 'INTEGER', false, null, null);
        $this->addColumn('LAST_PRICE_CHANGED', 'LastPriceChanged', 'TIMESTAMP', false, null, null);
        $this->addColumn('SALE_PRICE_CHANGE_LAST_HOUR', 'SalePriceChangeLastHour', 'INTEGER', false, null, 0);
        $this->addColumn('OFFER_PRICE_CHANGE_LAST_HOUR', 'OfferPriceChangeLastHour', 'INTEGER', false, null, 0);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('ItemType', 'GW2Spidy\\DB\\ItemType', RelationMap::MANY_TO_ONE, array('item_type_id' => 'id', ), null, null);
        $this->addRelation('ItemSubType', 'GW2Spidy\\DB\\ItemSubType', RelationMap::MANY_TO_ONE, array('item_sub_type_id' => 'id', ), null, null);
        $this->addRelation('ResultOfRecipe', 'GW2Spidy\\DB\\Recipe', RelationMap::ONE_TO_MANY, array('data_id' => 'result_item_id', ), null, null, 'ResultOfRecipes');
        $this->addRelation('Ingredient', 'GW2Spidy\\DB\\RecipeIngredient', RelationMap::ONE_TO_MANY, array('data_id' => 'item_id', ), null, null, 'Ingredients');
        $this->addRelation('SellListing', 'GW2Spidy\\DB\\SellListing', RelationMap::ONE_TO_MANY, array('data_id' => 'item_id', ), null, null, 'SellListings');
        $this->addRelation('BuyListing', 'GW2Spidy\\DB\\BuyListing', RelationMap::ONE_TO_MANY, array('data_id' => 'item_id', ), null, null, 'BuyListings');
        $this->addRelation('OnWatchlist', 'GW2Spidy\\DB\\Watchlist', RelationMap::ONE_TO_MANY, array('data_id' => 'item_id', ), null, null, 'OnWatchlists');
        $this->addRelation('Recipe', 'GW2Spidy\\DB\\Recipe', RelationMap::MANY_TO_MANY, array(), null, null, 'Recipes');
        $this->addRelation('User', 'GW2Spidy\\DB\\User', RelationMap::MANY_TO_MANY, array(), null, null, 'Users');
    } // buildRelations()

} // ItemTableMap
