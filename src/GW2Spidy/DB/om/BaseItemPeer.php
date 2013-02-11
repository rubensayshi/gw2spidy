<?php

namespace GW2Spidy\DB\om;

use \BasePeer;
use \Criteria;
use \PDO;
use \PDOStatement;
use \Propel;
use \PropelException;
use \PropelPDO;
use GW2Spidy\DB\BuyListingPeer;
use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemPeer;
use GW2Spidy\DB\ItemSubTypePeer;
use GW2Spidy\DB\ItemTypePeer;
use GW2Spidy\DB\RecipeIngredientPeer;
use GW2Spidy\DB\RecipePeer;
use GW2Spidy\DB\SellListingPeer;
use GW2Spidy\DB\WatchlistPeer;
use GW2Spidy\DB\map\ItemTableMap;

/**
 * Base static class for performing query and update operations on the 'item' table.
 *
 * 
 *
 * @package    propel.generator.gw2spidy.om
 */
abstract class BaseItemPeer {

    /** the default database name for this class */
    const DATABASE_NAME = 'gw2spidy';

    /** the table name for this class */
    const TABLE_NAME = 'item';

    /** the related Propel class for this table */
    const OM_CLASS = 'GW2Spidy\\DB\\Item';

    /** the related TableMap class for this table */
    const TM_CLASS = 'ItemTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 27;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 27;

    /** the column name for the DATA_ID field */
    const DATA_ID = 'item.DATA_ID';

    /** the column name for the TYPE_ID field */
    const TYPE_ID = 'item.TYPE_ID';

    /** the column name for the NAME field */
    const NAME = 'item.NAME';

    /** the column name for the TP_NAME field */
    const TP_NAME = 'item.TP_NAME';

    /** the column name for the CLEAN_NAME field */
    const CLEAN_NAME = 'item.CLEAN_NAME';

    /** the column name for the CLEAN_TP_NAME field */
    const CLEAN_TP_NAME = 'item.CLEAN_TP_NAME';

    /** the column name for the GEM_STORE_DESCRIPTION field */
    const GEM_STORE_DESCRIPTION = 'item.GEM_STORE_DESCRIPTION';

    /** the column name for the GEM_STORE_BLURB field */
    const GEM_STORE_BLURB = 'item.GEM_STORE_BLURB';

    /** the column name for the RESTRICTION_LEVEL field */
    const RESTRICTION_LEVEL = 'item.RESTRICTION_LEVEL';

    /** the column name for the RARITY field */
    const RARITY = 'item.RARITY';

    /** the column name for the VENDOR_SELL_PRICE field */
    const VENDOR_SELL_PRICE = 'item.VENDOR_SELL_PRICE';

    /** the column name for the VENDOR_PRICE field */
    const VENDOR_PRICE = 'item.VENDOR_PRICE';

    /** the column name for the KARMA_PRICE field */
    const KARMA_PRICE = 'item.KARMA_PRICE';

    /** the column name for the IMG field */
    const IMG = 'item.IMG';

    /** the column name for the RARITY_WORD field */
    const RARITY_WORD = 'item.RARITY_WORD';

    /** the column name for the ITEM_TYPE_ID field */
    const ITEM_TYPE_ID = 'item.ITEM_TYPE_ID';

    /** the column name for the ITEM_SUB_TYPE_ID field */
    const ITEM_SUB_TYPE_ID = 'item.ITEM_SUB_TYPE_ID';

    /** the column name for the MAX_OFFER_UNIT_PRICE field */
    const MAX_OFFER_UNIT_PRICE = 'item.MAX_OFFER_UNIT_PRICE';

    /** the column name for the MIN_SALE_UNIT_PRICE field */
    const MIN_SALE_UNIT_PRICE = 'item.MIN_SALE_UNIT_PRICE';

    /** the column name for the OFFER_AVAILABILITY field */
    const OFFER_AVAILABILITY = 'item.OFFER_AVAILABILITY';

    /** the column name for the SALE_AVAILABILITY field */
    const SALE_AVAILABILITY = 'item.SALE_AVAILABILITY';

    /** the column name for the GW2DB_ID field */
    const GW2DB_ID = 'item.GW2DB_ID';

    /** the column name for the GW2DB_EXTERNAL_ID field */
    const GW2DB_EXTERNAL_ID = 'item.GW2DB_EXTERNAL_ID';

    /** the column name for the LAST_PRICE_CHANGED field */
    const LAST_PRICE_CHANGED = 'item.LAST_PRICE_CHANGED';

    /** the column name for the LAST_UPDATED field */
    const LAST_UPDATED = 'item.LAST_UPDATED';

    /** the column name for the SALE_PRICE_CHANGE_LAST_HOUR field */
    const SALE_PRICE_CHANGE_LAST_HOUR = 'item.SALE_PRICE_CHANGE_LAST_HOUR';

    /** the column name for the OFFER_PRICE_CHANGE_LAST_HOUR field */
    const OFFER_PRICE_CHANGE_LAST_HOUR = 'item.OFFER_PRICE_CHANGE_LAST_HOUR';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identiy map to hold any loaded instances of Item objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array Item[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. ItemPeer::$fieldNames[ItemPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('DataId', 'TypeId', 'Name', 'TpName', 'CleanName', 'CleanTpName', 'GemStoreDescription', 'GemStoreBlurb', 'RestrictionLevel', 'Rarity', 'VendorSellPrice', 'VendorPrice', 'KarmaPrice', 'Img', 'RarityWord', 'ItemTypeId', 'ItemSubTypeId', 'MaxOfferUnitPrice', 'MinSaleUnitPrice', 'OfferAvailability', 'SaleAvailability', 'Gw2dbId', 'Gw2dbExternalId', 'LastPriceChanged', 'LastUpdated', 'SalePriceChangeLastHour', 'OfferPriceChangeLastHour', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('dataId', 'typeId', 'name', 'tpName', 'cleanName', 'cleanTpName', 'gemStoreDescription', 'gemStoreBlurb', 'restrictionLevel', 'rarity', 'vendorSellPrice', 'vendorPrice', 'karmaPrice', 'img', 'rarityWord', 'itemTypeId', 'itemSubTypeId', 'maxOfferUnitPrice', 'minSaleUnitPrice', 'offerAvailability', 'saleAvailability', 'gw2dbId', 'gw2dbExternalId', 'lastPriceChanged', 'lastUpdated', 'salePriceChangeLastHour', 'offerPriceChangeLastHour', ),
        BasePeer::TYPE_COLNAME => array (ItemPeer::DATA_ID, ItemPeer::TYPE_ID, ItemPeer::NAME, ItemPeer::TP_NAME, ItemPeer::CLEAN_NAME, ItemPeer::CLEAN_TP_NAME, ItemPeer::GEM_STORE_DESCRIPTION, ItemPeer::GEM_STORE_BLURB, ItemPeer::RESTRICTION_LEVEL, ItemPeer::RARITY, ItemPeer::VENDOR_SELL_PRICE, ItemPeer::VENDOR_PRICE, ItemPeer::KARMA_PRICE, ItemPeer::IMG, ItemPeer::RARITY_WORD, ItemPeer::ITEM_TYPE_ID, ItemPeer::ITEM_SUB_TYPE_ID, ItemPeer::MAX_OFFER_UNIT_PRICE, ItemPeer::MIN_SALE_UNIT_PRICE, ItemPeer::OFFER_AVAILABILITY, ItemPeer::SALE_AVAILABILITY, ItemPeer::GW2DB_ID, ItemPeer::GW2DB_EXTERNAL_ID, ItemPeer::LAST_PRICE_CHANGED, ItemPeer::LAST_UPDATED, ItemPeer::SALE_PRICE_CHANGE_LAST_HOUR, ItemPeer::OFFER_PRICE_CHANGE_LAST_HOUR, ),
        BasePeer::TYPE_RAW_COLNAME => array ('DATA_ID', 'TYPE_ID', 'NAME', 'TP_NAME', 'CLEAN_NAME', 'CLEAN_TP_NAME', 'GEM_STORE_DESCRIPTION', 'GEM_STORE_BLURB', 'RESTRICTION_LEVEL', 'RARITY', 'VENDOR_SELL_PRICE', 'VENDOR_PRICE', 'KARMA_PRICE', 'IMG', 'RARITY_WORD', 'ITEM_TYPE_ID', 'ITEM_SUB_TYPE_ID', 'MAX_OFFER_UNIT_PRICE', 'MIN_SALE_UNIT_PRICE', 'OFFER_AVAILABILITY', 'SALE_AVAILABILITY', 'GW2DB_ID', 'GW2DB_EXTERNAL_ID', 'LAST_PRICE_CHANGED', 'LAST_UPDATED', 'SALE_PRICE_CHANGE_LAST_HOUR', 'OFFER_PRICE_CHANGE_LAST_HOUR', ),
        BasePeer::TYPE_FIELDNAME => array ('data_id', 'type_id', 'name', 'tp_name', 'clean_name', 'clean_tp_name', 'gem_store_description', 'gem_store_blurb', 'restriction_level', 'rarity', 'vendor_sell_price', 'vendor_price', 'karma_price', 'img', 'rarity_word', 'item_type_id', 'item_sub_type_id', 'max_offer_unit_price', 'min_sale_unit_price', 'offer_availability', 'sale_availability', 'gw2db_id', 'gw2db_external_id', 'last_price_changed', 'last_updated', 'sale_price_change_last_hour', 'offer_price_change_last_hour', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. ItemPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('DataId' => 0, 'TypeId' => 1, 'Name' => 2, 'TpName' => 3, 'CleanName' => 4, 'CleanTpName' => 5, 'GemStoreDescription' => 6, 'GemStoreBlurb' => 7, 'RestrictionLevel' => 8, 'Rarity' => 9, 'VendorSellPrice' => 10, 'VendorPrice' => 11, 'KarmaPrice' => 12, 'Img' => 13, 'RarityWord' => 14, 'ItemTypeId' => 15, 'ItemSubTypeId' => 16, 'MaxOfferUnitPrice' => 17, 'MinSaleUnitPrice' => 18, 'OfferAvailability' => 19, 'SaleAvailability' => 20, 'Gw2dbId' => 21, 'Gw2dbExternalId' => 22, 'LastPriceChanged' => 23, 'LastUpdated' => 24, 'SalePriceChangeLastHour' => 25, 'OfferPriceChangeLastHour' => 26, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('dataId' => 0, 'typeId' => 1, 'name' => 2, 'tpName' => 3, 'cleanName' => 4, 'cleanTpName' => 5, 'gemStoreDescription' => 6, 'gemStoreBlurb' => 7, 'restrictionLevel' => 8, 'rarity' => 9, 'vendorSellPrice' => 10, 'vendorPrice' => 11, 'karmaPrice' => 12, 'img' => 13, 'rarityWord' => 14, 'itemTypeId' => 15, 'itemSubTypeId' => 16, 'maxOfferUnitPrice' => 17, 'minSaleUnitPrice' => 18, 'offerAvailability' => 19, 'saleAvailability' => 20, 'gw2dbId' => 21, 'gw2dbExternalId' => 22, 'lastPriceChanged' => 23, 'lastUpdated' => 24, 'salePriceChangeLastHour' => 25, 'offerPriceChangeLastHour' => 26, ),
        BasePeer::TYPE_COLNAME => array (ItemPeer::DATA_ID => 0, ItemPeer::TYPE_ID => 1, ItemPeer::NAME => 2, ItemPeer::TP_NAME => 3, ItemPeer::CLEAN_NAME => 4, ItemPeer::CLEAN_TP_NAME => 5, ItemPeer::GEM_STORE_DESCRIPTION => 6, ItemPeer::GEM_STORE_BLURB => 7, ItemPeer::RESTRICTION_LEVEL => 8, ItemPeer::RARITY => 9, ItemPeer::VENDOR_SELL_PRICE => 10, ItemPeer::VENDOR_PRICE => 11, ItemPeer::KARMA_PRICE => 12, ItemPeer::IMG => 13, ItemPeer::RARITY_WORD => 14, ItemPeer::ITEM_TYPE_ID => 15, ItemPeer::ITEM_SUB_TYPE_ID => 16, ItemPeer::MAX_OFFER_UNIT_PRICE => 17, ItemPeer::MIN_SALE_UNIT_PRICE => 18, ItemPeer::OFFER_AVAILABILITY => 19, ItemPeer::SALE_AVAILABILITY => 20, ItemPeer::GW2DB_ID => 21, ItemPeer::GW2DB_EXTERNAL_ID => 22, ItemPeer::LAST_PRICE_CHANGED => 23, ItemPeer::LAST_UPDATED => 24, ItemPeer::SALE_PRICE_CHANGE_LAST_HOUR => 25, ItemPeer::OFFER_PRICE_CHANGE_LAST_HOUR => 26, ),
        BasePeer::TYPE_RAW_COLNAME => array ('DATA_ID' => 0, 'TYPE_ID' => 1, 'NAME' => 2, 'TP_NAME' => 3, 'CLEAN_NAME' => 4, 'CLEAN_TP_NAME' => 5, 'GEM_STORE_DESCRIPTION' => 6, 'GEM_STORE_BLURB' => 7, 'RESTRICTION_LEVEL' => 8, 'RARITY' => 9, 'VENDOR_SELL_PRICE' => 10, 'VENDOR_PRICE' => 11, 'KARMA_PRICE' => 12, 'IMG' => 13, 'RARITY_WORD' => 14, 'ITEM_TYPE_ID' => 15, 'ITEM_SUB_TYPE_ID' => 16, 'MAX_OFFER_UNIT_PRICE' => 17, 'MIN_SALE_UNIT_PRICE' => 18, 'OFFER_AVAILABILITY' => 19, 'SALE_AVAILABILITY' => 20, 'GW2DB_ID' => 21, 'GW2DB_EXTERNAL_ID' => 22, 'LAST_PRICE_CHANGED' => 23, 'LAST_UPDATED' => 24, 'SALE_PRICE_CHANGE_LAST_HOUR' => 25, 'OFFER_PRICE_CHANGE_LAST_HOUR' => 26, ),
        BasePeer::TYPE_FIELDNAME => array ('data_id' => 0, 'type_id' => 1, 'name' => 2, 'tp_name' => 3, 'clean_name' => 4, 'clean_tp_name' => 5, 'gem_store_description' => 6, 'gem_store_blurb' => 7, 'restriction_level' => 8, 'rarity' => 9, 'vendor_sell_price' => 10, 'vendor_price' => 11, 'karma_price' => 12, 'img' => 13, 'rarity_word' => 14, 'item_type_id' => 15, 'item_sub_type_id' => 16, 'max_offer_unit_price' => 17, 'min_sale_unit_price' => 18, 'offer_availability' => 19, 'sale_availability' => 20, 'gw2db_id' => 21, 'gw2db_external_id' => 22, 'last_price_changed' => 23, 'last_updated' => 24, 'sale_price_change_last_hour' => 25, 'offer_price_change_last_hour' => 26, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, )
    );

    /**
     * Translates a fieldname to another type
     *
     * @param      string $name field name
     * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @param      string $toType   One of the class type constants
     * @return string          translated name of the field.
     * @throws PropelException - if the specified name could not be found in the fieldname mappings.
     */
    public static function translateFieldName($name, $fromType, $toType)
    {
        $toNames = ItemPeer::getFieldNames($toType);
        $key = isset(ItemPeer::$fieldKeys[$fromType][$name]) ? ItemPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(ItemPeer::$fieldKeys[$fromType], true));
        }

        return $toNames[$key];
    }

    /**
     * Returns an array of field names.
     *
     * @param      string $type The type of fieldnames to return:
     *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @return array           A list of field names
     * @throws PropelException - if the type is not valid.
     */
    public static function getFieldNames($type = BasePeer::TYPE_PHPNAME)
    {
        if (!array_key_exists($type, ItemPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return ItemPeer::$fieldNames[$type];
    }

    /**
     * Convenience method which changes table.column to alias.column.
     *
     * Using this method you can maintain SQL abstraction while using column aliases.
     * <code>
     *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
     *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
     * </code>
     * @param      string $alias The alias for the current table.
     * @param      string $column The column name for current table. (i.e. ItemPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(ItemPeer::TABLE_NAME.'.', $alias.'.', $column);
    }

    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param      Criteria $criteria object containing the columns to add.
     * @param      string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(ItemPeer::DATA_ID);
            $criteria->addSelectColumn(ItemPeer::TYPE_ID);
            $criteria->addSelectColumn(ItemPeer::NAME);
            $criteria->addSelectColumn(ItemPeer::TP_NAME);
            $criteria->addSelectColumn(ItemPeer::CLEAN_NAME);
            $criteria->addSelectColumn(ItemPeer::CLEAN_TP_NAME);
            $criteria->addSelectColumn(ItemPeer::GEM_STORE_DESCRIPTION);
            $criteria->addSelectColumn(ItemPeer::GEM_STORE_BLURB);
            $criteria->addSelectColumn(ItemPeer::RESTRICTION_LEVEL);
            $criteria->addSelectColumn(ItemPeer::RARITY);
            $criteria->addSelectColumn(ItemPeer::VENDOR_SELL_PRICE);
            $criteria->addSelectColumn(ItemPeer::VENDOR_PRICE);
            $criteria->addSelectColumn(ItemPeer::KARMA_PRICE);
            $criteria->addSelectColumn(ItemPeer::IMG);
            $criteria->addSelectColumn(ItemPeer::RARITY_WORD);
            $criteria->addSelectColumn(ItemPeer::ITEM_TYPE_ID);
            $criteria->addSelectColumn(ItemPeer::ITEM_SUB_TYPE_ID);
            $criteria->addSelectColumn(ItemPeer::MAX_OFFER_UNIT_PRICE);
            $criteria->addSelectColumn(ItemPeer::MIN_SALE_UNIT_PRICE);
            $criteria->addSelectColumn(ItemPeer::OFFER_AVAILABILITY);
            $criteria->addSelectColumn(ItemPeer::SALE_AVAILABILITY);
            $criteria->addSelectColumn(ItemPeer::GW2DB_ID);
            $criteria->addSelectColumn(ItemPeer::GW2DB_EXTERNAL_ID);
            $criteria->addSelectColumn(ItemPeer::LAST_PRICE_CHANGED);
            $criteria->addSelectColumn(ItemPeer::LAST_UPDATED);
            $criteria->addSelectColumn(ItemPeer::SALE_PRICE_CHANGE_LAST_HOUR);
            $criteria->addSelectColumn(ItemPeer::OFFER_PRICE_CHANGE_LAST_HOUR);
        } else {
            $criteria->addSelectColumn($alias . '.DATA_ID');
            $criteria->addSelectColumn($alias . '.TYPE_ID');
            $criteria->addSelectColumn($alias . '.NAME');
            $criteria->addSelectColumn($alias . '.TP_NAME');
            $criteria->addSelectColumn($alias . '.CLEAN_NAME');
            $criteria->addSelectColumn($alias . '.CLEAN_TP_NAME');
            $criteria->addSelectColumn($alias . '.GEM_STORE_DESCRIPTION');
            $criteria->addSelectColumn($alias . '.GEM_STORE_BLURB');
            $criteria->addSelectColumn($alias . '.RESTRICTION_LEVEL');
            $criteria->addSelectColumn($alias . '.RARITY');
            $criteria->addSelectColumn($alias . '.VENDOR_SELL_PRICE');
            $criteria->addSelectColumn($alias . '.VENDOR_PRICE');
            $criteria->addSelectColumn($alias . '.KARMA_PRICE');
            $criteria->addSelectColumn($alias . '.IMG');
            $criteria->addSelectColumn($alias . '.RARITY_WORD');
            $criteria->addSelectColumn($alias . '.ITEM_TYPE_ID');
            $criteria->addSelectColumn($alias . '.ITEM_SUB_TYPE_ID');
            $criteria->addSelectColumn($alias . '.MAX_OFFER_UNIT_PRICE');
            $criteria->addSelectColumn($alias . '.MIN_SALE_UNIT_PRICE');
            $criteria->addSelectColumn($alias . '.OFFER_AVAILABILITY');
            $criteria->addSelectColumn($alias . '.SALE_AVAILABILITY');
            $criteria->addSelectColumn($alias . '.GW2DB_ID');
            $criteria->addSelectColumn($alias . '.GW2DB_EXTERNAL_ID');
            $criteria->addSelectColumn($alias . '.LAST_PRICE_CHANGED');
            $criteria->addSelectColumn($alias . '.LAST_UPDATED');
            $criteria->addSelectColumn($alias . '.SALE_PRICE_CHANGE_LAST_HOUR');
            $criteria->addSelectColumn($alias . '.OFFER_PRICE_CHANGE_LAST_HOUR');
        }
    }

    /**
     * Returns the number of rows matching criteria.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @return int Number of matching rows.
     */
    public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
    {
        // we may modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(ItemPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            ItemPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(ItemPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        // BasePeer returns a PDOStatement
        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }
    /**
     * Selects one object from the DB.
     *
     * @param      Criteria $criteria object used to create the SELECT statement.
     * @param      PropelPDO $con
     * @return                 Item
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = ItemPeer::doSelect($critcopy, $con);
        if ($objects) {
            return $objects[0];
        }

        return null;
    }
    /**
     * Selects several row from the DB.
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con
     * @return array           Array of selected Objects
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelect(Criteria $criteria, PropelPDO $con = null)
    {
        return ItemPeer::populateObjects(ItemPeer::doSelectStmt($criteria, $con));
    }
    /**
     * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
     *
     * Use this method directly if you want to work with an executed statement durirectly (for example
     * to perform your own object hydration).
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con The connection to use
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return PDOStatement The executed PDOStatement object.
     * @see        BasePeer::doSelect()
     */
    public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            ItemPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(ItemPeer::DATABASE_NAME);

        // BasePeer returns a PDOStatement
        return BasePeer::doSelect($criteria, $con);
    }
    /**
     * Adds an object to the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doSelect*()
     * methods in your stub classes -- you may need to explicitly add objects
     * to the cache in order to ensure that the same objects are always returned by doSelect*()
     * and retrieveByPK*() calls.
     *
     * @param      Item $obj A Item object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getDataId();
            } // if key === null
            ItemPeer::$instances[$key] = $obj;
        }
    }

    /**
     * Removes an object from the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doDelete
     * methods in your stub classes -- you may need to explicitly remove objects
     * from the cache in order to prevent returning objects that no longer exist.
     *
     * @param      mixed $value A Item object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof Item) {
                $key = (string) $value->getDataId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or Item object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(ItemPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return   Item Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(ItemPeer::$instances[$key])) {
                return ItemPeer::$instances[$key];
            }
        }

        return null; // just to be explicit
    }
    
    /**
     * Clear the instance pool.
     *
     * @return void
     */
    public static function clearInstancePool()
    {
        ItemPeer::$instances = array();
    }
    
    /**
     * Method to invalidate the instance pool of all tables related to item
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return string A string version of PK or NULL if the components of primary key in result array are all null.
     */
    public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[$startcol] === null) {
            return null;
        }

        return (string) $row[$startcol];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $startcol = 0)
    {

        return (int) $row[$startcol];
    }
    
    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function populateObjects(PDOStatement $stmt)
    {
        $results = array();
    
        // set the class once to avoid overhead in the loop
        $cls = ItemPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = ItemPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = ItemPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                ItemPeer::addInstanceToPool($obj, $key);
            } // if key exists
        }
        $stmt->closeCursor();

        return $results;
    }
    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return array (Item object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = ItemPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = ItemPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + ItemPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = ItemPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            ItemPeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }


    /**
     * Returns the number of rows matching criteria, joining the related ItemType table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinItemType(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(ItemPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            ItemPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(ItemPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(ItemPeer::ITEM_TYPE_ID, ItemTypePeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related ItemSubType table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinItemSubType(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(ItemPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            ItemPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(ItemPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(ItemPeer::ITEM_SUB_TYPE_ID, ItemSubTypePeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Selects a collection of Item objects pre-filled with their ItemType objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Item objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinItemType(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(ItemPeer::DATABASE_NAME);
        }

        ItemPeer::addSelectColumns($criteria);
        $startcol = ItemPeer::NUM_HYDRATE_COLUMNS;
        ItemTypePeer::addSelectColumns($criteria);

        $criteria->addJoin(ItemPeer::ITEM_TYPE_ID, ItemTypePeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = ItemPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = ItemPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = ItemPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                ItemPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = ItemTypePeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = ItemTypePeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = ItemTypePeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    ItemTypePeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Item) to $obj2 (ItemType)
                $obj2->addItem($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Item objects pre-filled with their ItemSubType objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Item objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinItemSubType(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(ItemPeer::DATABASE_NAME);
        }

        ItemPeer::addSelectColumns($criteria);
        $startcol = ItemPeer::NUM_HYDRATE_COLUMNS;
        ItemSubTypePeer::addSelectColumns($criteria);

        $criteria->addJoin(ItemPeer::ITEM_SUB_TYPE_ID, ItemSubTypePeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = ItemPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = ItemPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = ItemPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                ItemPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = ItemSubTypePeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = ItemSubTypePeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = ItemSubTypePeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    ItemSubTypePeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Item) to $obj2 (ItemSubType)
                $obj2->addItem($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining all related tables
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAll(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(ItemPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            ItemPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(ItemPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(ItemPeer::ITEM_TYPE_ID, ItemTypePeer::ID, $join_behavior);

        $criteria->addJoin(ItemPeer::ITEM_SUB_TYPE_ID, ItemSubTypePeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }

    /**
     * Selects a collection of Item objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Item objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(ItemPeer::DATABASE_NAME);
        }

        ItemPeer::addSelectColumns($criteria);
        $startcol2 = ItemPeer::NUM_HYDRATE_COLUMNS;

        ItemTypePeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + ItemTypePeer::NUM_HYDRATE_COLUMNS;

        ItemSubTypePeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + ItemSubTypePeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(ItemPeer::ITEM_TYPE_ID, ItemTypePeer::ID, $join_behavior);

        $criteria->addJoin(ItemPeer::ITEM_SUB_TYPE_ID, ItemSubTypePeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = ItemPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = ItemPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = ItemPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                ItemPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            // Add objects for joined ItemType rows

            $key2 = ItemTypePeer::getPrimaryKeyHashFromRow($row, $startcol2);
            if ($key2 !== null) {
                $obj2 = ItemTypePeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = ItemTypePeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    ItemTypePeer::addInstanceToPool($obj2, $key2);
                } // if obj2 loaded

                // Add the $obj1 (Item) to the collection in $obj2 (ItemType)
                $obj2->addItem($obj1);
            } // if joined row not null

            // Add objects for joined ItemSubType rows

            $key3 = ItemSubTypePeer::getPrimaryKeyHashFromRow($row, $startcol3);
            if ($key3 !== null) {
                $obj3 = ItemSubTypePeer::getInstanceFromPool($key3);
                if (!$obj3) {

                    $cls = ItemSubTypePeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    ItemSubTypePeer::addInstanceToPool($obj3, $key3);
                } // if obj3 loaded

                // Add the $obj1 (Item) to the collection in $obj3 (ItemSubType)
                $obj3->addItem($obj1);
            } // if joined row not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining the related ItemType table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptItemType(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(ItemPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            ItemPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(ItemPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
    
        $criteria->addJoin(ItemPeer::ITEM_SUB_TYPE_ID, ItemSubTypePeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related ItemSubType table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptItemSubType(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(ItemPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            ItemPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(ItemPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
    
        $criteria->addJoin(ItemPeer::ITEM_TYPE_ID, ItemTypePeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Selects a collection of Item objects pre-filled with all related objects except ItemType.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Item objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptItemType(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(ItemPeer::DATABASE_NAME);
        }

        ItemPeer::addSelectColumns($criteria);
        $startcol2 = ItemPeer::NUM_HYDRATE_COLUMNS;

        ItemSubTypePeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + ItemSubTypePeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(ItemPeer::ITEM_SUB_TYPE_ID, ItemSubTypePeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = ItemPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = ItemPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = ItemPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                ItemPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined ItemSubType rows

                $key2 = ItemSubTypePeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = ItemSubTypePeer::getInstanceFromPool($key2);
                    if (!$obj2) {
    
                        $cls = ItemSubTypePeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    ItemSubTypePeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (Item) to the collection in $obj2 (ItemSubType)
                $obj2->addItem($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Item objects pre-filled with all related objects except ItemSubType.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Item objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptItemSubType(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(ItemPeer::DATABASE_NAME);
        }

        ItemPeer::addSelectColumns($criteria);
        $startcol2 = ItemPeer::NUM_HYDRATE_COLUMNS;

        ItemTypePeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + ItemTypePeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(ItemPeer::ITEM_TYPE_ID, ItemTypePeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = ItemPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = ItemPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = ItemPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                ItemPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined ItemType rows

                $key2 = ItemTypePeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = ItemTypePeer::getInstanceFromPool($key2);
                    if (!$obj2) {
    
                        $cls = ItemTypePeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    ItemTypePeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (Item) to the collection in $obj2 (ItemType)
                $obj2->addItem($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }

    /**
     * Returns the TableMap related to this peer.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getDatabaseMap(ItemPeer::DATABASE_NAME)->getTable(ItemPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseItemPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseItemPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new ItemTableMap());
      }
    }

    /**
     * The class that the Peer will make instances of.
     *
     *
     * @return string ClassName
     */
    public static function getOMClass()
    {
        return ItemPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a Item or Criteria object.
     *
     * @param      mixed $values Criteria or Item object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from Item object
        }


        // Set the correct dbName
        $criteria->setDbName(ItemPeer::DATABASE_NAME);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = BasePeer::doInsert($criteria, $con);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

    /**
     * Performs an UPDATE on the database, given a Item or Criteria object.
     *
     * @param      mixed $values Criteria or Item object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(ItemPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(ItemPeer::DATA_ID);
            $value = $criteria->remove(ItemPeer::DATA_ID);
            if ($value) {
                $selectCriteria->add(ItemPeer::DATA_ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(ItemPeer::TABLE_NAME);
            }

        } else { // $values is Item object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(ItemPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the item table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(ItemPeer::TABLE_NAME, $con, ItemPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            ItemPeer::clearInstancePool();
            ItemPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a Item or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Item object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param      PropelPDO $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *				if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, PropelPDO $con = null)
     {
        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            ItemPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof Item) { // it's a model object
            // invalidate the cache for this single object
            ItemPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(ItemPeer::DATABASE_NAME);
            $criteria->add(ItemPeer::DATA_ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                ItemPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(ItemPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            
            $affectedRows += BasePeer::doDelete($criteria, $con);
            ItemPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given Item object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      Item $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(ItemPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(ItemPeer::TABLE_NAME);

            if (! is_array($cols)) {
                $cols = array($cols);
            }

            foreach ($cols as $colName) {
                if ($tableMap->hasColumn($colName)) {
                    $get = 'get' . $tableMap->getColumn($colName)->getPhpName();
                    $columns[$colName] = $obj->$get();
                }
            }
        } else {

        }

        return BasePeer::doValidate(ItemPeer::DATABASE_NAME, ItemPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return Item
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = ItemPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(ItemPeer::DATABASE_NAME);
        $criteria->add(ItemPeer::DATA_ID, $pk);

        $v = ItemPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return Item[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(ItemPeer::DATABASE_NAME);
            $criteria->add(ItemPeer::DATA_ID, $pks, Criteria::IN);
            $objs = ItemPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseItemPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseItemPeer::buildTableMap();

