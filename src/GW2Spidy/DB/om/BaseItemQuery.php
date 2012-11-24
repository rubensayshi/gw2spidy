<?php

namespace GW2Spidy\DB\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use GW2Spidy\DB\BuyListing;
use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemPeer;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\ItemSubType;
use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\Recipe;
use GW2Spidy\DB\RecipeIngredient;
use GW2Spidy\DB\SellListing;
use GW2Spidy\DB\User;
use GW2Spidy\DB\Watchlist;

/**
 * Base class that represents a query for the 'item' table.
 *
 * 
 *
 * @method     ItemQuery orderByDataId($order = Criteria::ASC) Order by the data_id column
 * @method     ItemQuery orderByTypeId($order = Criteria::ASC) Order by the type_id column
 * @method     ItemQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ItemQuery orderByGemStoreDescription($order = Criteria::ASC) Order by the gem_store_description column
 * @method     ItemQuery orderByGemStoreBlurb($order = Criteria::ASC) Order by the gem_store_blurb column
 * @method     ItemQuery orderByRestrictionLevel($order = Criteria::ASC) Order by the restriction_level column
 * @method     ItemQuery orderByRarity($order = Criteria::ASC) Order by the rarity column
 * @method     ItemQuery orderByVendorSellPrice($order = Criteria::ASC) Order by the vendor_sell_price column
 * @method     ItemQuery orderByVendorPrice($order = Criteria::ASC) Order by the vendor_price column
 * @method     ItemQuery orderByKarmaPrice($order = Criteria::ASC) Order by the karma_price column
 * @method     ItemQuery orderByImg($order = Criteria::ASC) Order by the img column
 * @method     ItemQuery orderByRarityWord($order = Criteria::ASC) Order by the rarity_word column
 * @method     ItemQuery orderByItemTypeId($order = Criteria::ASC) Order by the item_type_id column
 * @method     ItemQuery orderByItemSubTypeId($order = Criteria::ASC) Order by the item_sub_type_id column
 * @method     ItemQuery orderByMaxOfferUnitPrice($order = Criteria::ASC) Order by the max_offer_unit_price column
 * @method     ItemQuery orderByMinSaleUnitPrice($order = Criteria::ASC) Order by the min_sale_unit_price column
 * @method     ItemQuery orderByOfferAvailability($order = Criteria::ASC) Order by the offer_availability column
 * @method     ItemQuery orderBySaleAvailability($order = Criteria::ASC) Order by the sale_availability column
 * @method     ItemQuery orderByGw2dbId($order = Criteria::ASC) Order by the gw2db_id column
 * @method     ItemQuery orderByGw2dbExternalId($order = Criteria::ASC) Order by the gw2db_external_id column
 * @method     ItemQuery orderByLastPriceChanged($order = Criteria::ASC) Order by the last_price_changed column
 * @method     ItemQuery orderBySalePriceChangeLastHour($order = Criteria::ASC) Order by the sale_price_change_last_hour column
 * @method     ItemQuery orderByOfferPriceChangeLastHour($order = Criteria::ASC) Order by the offer_price_change_last_hour column
 *
 * @method     ItemQuery groupByDataId() Group by the data_id column
 * @method     ItemQuery groupByTypeId() Group by the type_id column
 * @method     ItemQuery groupByName() Group by the name column
 * @method     ItemQuery groupByGemStoreDescription() Group by the gem_store_description column
 * @method     ItemQuery groupByGemStoreBlurb() Group by the gem_store_blurb column
 * @method     ItemQuery groupByRestrictionLevel() Group by the restriction_level column
 * @method     ItemQuery groupByRarity() Group by the rarity column
 * @method     ItemQuery groupByVendorSellPrice() Group by the vendor_sell_price column
 * @method     ItemQuery groupByVendorPrice() Group by the vendor_price column
 * @method     ItemQuery groupByKarmaPrice() Group by the karma_price column
 * @method     ItemQuery groupByImg() Group by the img column
 * @method     ItemQuery groupByRarityWord() Group by the rarity_word column
 * @method     ItemQuery groupByItemTypeId() Group by the item_type_id column
 * @method     ItemQuery groupByItemSubTypeId() Group by the item_sub_type_id column
 * @method     ItemQuery groupByMaxOfferUnitPrice() Group by the max_offer_unit_price column
 * @method     ItemQuery groupByMinSaleUnitPrice() Group by the min_sale_unit_price column
 * @method     ItemQuery groupByOfferAvailability() Group by the offer_availability column
 * @method     ItemQuery groupBySaleAvailability() Group by the sale_availability column
 * @method     ItemQuery groupByGw2dbId() Group by the gw2db_id column
 * @method     ItemQuery groupByGw2dbExternalId() Group by the gw2db_external_id column
 * @method     ItemQuery groupByLastPriceChanged() Group by the last_price_changed column
 * @method     ItemQuery groupBySalePriceChangeLastHour() Group by the sale_price_change_last_hour column
 * @method     ItemQuery groupByOfferPriceChangeLastHour() Group by the offer_price_change_last_hour column
 *
 * @method     ItemQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ItemQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ItemQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ItemQuery leftJoinItemType($relationAlias = null) Adds a LEFT JOIN clause to the query using the ItemType relation
 * @method     ItemQuery rightJoinItemType($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ItemType relation
 * @method     ItemQuery innerJoinItemType($relationAlias = null) Adds a INNER JOIN clause to the query using the ItemType relation
 *
 * @method     ItemQuery leftJoinItemSubType($relationAlias = null) Adds a LEFT JOIN clause to the query using the ItemSubType relation
 * @method     ItemQuery rightJoinItemSubType($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ItemSubType relation
 * @method     ItemQuery innerJoinItemSubType($relationAlias = null) Adds a INNER JOIN clause to the query using the ItemSubType relation
 *
 * @method     ItemQuery leftJoinResultOfRecipe($relationAlias = null) Adds a LEFT JOIN clause to the query using the ResultOfRecipe relation
 * @method     ItemQuery rightJoinResultOfRecipe($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ResultOfRecipe relation
 * @method     ItemQuery innerJoinResultOfRecipe($relationAlias = null) Adds a INNER JOIN clause to the query using the ResultOfRecipe relation
 *
 * @method     ItemQuery leftJoinIngredient($relationAlias = null) Adds a LEFT JOIN clause to the query using the Ingredient relation
 * @method     ItemQuery rightJoinIngredient($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Ingredient relation
 * @method     ItemQuery innerJoinIngredient($relationAlias = null) Adds a INNER JOIN clause to the query using the Ingredient relation
 *
 * @method     ItemQuery leftJoinSellListing($relationAlias = null) Adds a LEFT JOIN clause to the query using the SellListing relation
 * @method     ItemQuery rightJoinSellListing($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SellListing relation
 * @method     ItemQuery innerJoinSellListing($relationAlias = null) Adds a INNER JOIN clause to the query using the SellListing relation
 *
 * @method     ItemQuery leftJoinBuyListing($relationAlias = null) Adds a LEFT JOIN clause to the query using the BuyListing relation
 * @method     ItemQuery rightJoinBuyListing($relationAlias = null) Adds a RIGHT JOIN clause to the query using the BuyListing relation
 * @method     ItemQuery innerJoinBuyListing($relationAlias = null) Adds a INNER JOIN clause to the query using the BuyListing relation
 *
 * @method     ItemQuery leftJoinOnWatchlist($relationAlias = null) Adds a LEFT JOIN clause to the query using the OnWatchlist relation
 * @method     ItemQuery rightJoinOnWatchlist($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OnWatchlist relation
 * @method     ItemQuery innerJoinOnWatchlist($relationAlias = null) Adds a INNER JOIN clause to the query using the OnWatchlist relation
 *
 * @method     Item findOne(PropelPDO $con = null) Return the first Item matching the query
 * @method     Item findOneOrCreate(PropelPDO $con = null) Return the first Item matching the query, or a new Item object populated from the query conditions when no match is found
 *
 * @method     Item findOneByDataId(int $data_id) Return the first Item filtered by the data_id column
 * @method     Item findOneByTypeId(int $type_id) Return the first Item filtered by the type_id column
 * @method     Item findOneByName(string $name) Return the first Item filtered by the name column
 * @method     Item findOneByGemStoreDescription(string $gem_store_description) Return the first Item filtered by the gem_store_description column
 * @method     Item findOneByGemStoreBlurb(string $gem_store_blurb) Return the first Item filtered by the gem_store_blurb column
 * @method     Item findOneByRestrictionLevel(int $restriction_level) Return the first Item filtered by the restriction_level column
 * @method     Item findOneByRarity(int $rarity) Return the first Item filtered by the rarity column
 * @method     Item findOneByVendorSellPrice(int $vendor_sell_price) Return the first Item filtered by the vendor_sell_price column
 * @method     Item findOneByVendorPrice(int $vendor_price) Return the first Item filtered by the vendor_price column
 * @method     Item findOneByKarmaPrice(int $karma_price) Return the first Item filtered by the karma_price column
 * @method     Item findOneByImg(string $img) Return the first Item filtered by the img column
 * @method     Item findOneByRarityWord(string $rarity_word) Return the first Item filtered by the rarity_word column
 * @method     Item findOneByItemTypeId(int $item_type_id) Return the first Item filtered by the item_type_id column
 * @method     Item findOneByItemSubTypeId(int $item_sub_type_id) Return the first Item filtered by the item_sub_type_id column
 * @method     Item findOneByMaxOfferUnitPrice(int $max_offer_unit_price) Return the first Item filtered by the max_offer_unit_price column
 * @method     Item findOneByMinSaleUnitPrice(int $min_sale_unit_price) Return the first Item filtered by the min_sale_unit_price column
 * @method     Item findOneByOfferAvailability(int $offer_availability) Return the first Item filtered by the offer_availability column
 * @method     Item findOneBySaleAvailability(int $sale_availability) Return the first Item filtered by the sale_availability column
 * @method     Item findOneByGw2dbId(int $gw2db_id) Return the first Item filtered by the gw2db_id column
 * @method     Item findOneByGw2dbExternalId(int $gw2db_external_id) Return the first Item filtered by the gw2db_external_id column
 * @method     Item findOneByLastPriceChanged(string $last_price_changed) Return the first Item filtered by the last_price_changed column
 * @method     Item findOneBySalePriceChangeLastHour(int $sale_price_change_last_hour) Return the first Item filtered by the sale_price_change_last_hour column
 * @method     Item findOneByOfferPriceChangeLastHour(int $offer_price_change_last_hour) Return the first Item filtered by the offer_price_change_last_hour column
 *
 * @method     array findByDataId(int $data_id) Return Item objects filtered by the data_id column
 * @method     array findByTypeId(int $type_id) Return Item objects filtered by the type_id column
 * @method     array findByName(string $name) Return Item objects filtered by the name column
 * @method     array findByGemStoreDescription(string $gem_store_description) Return Item objects filtered by the gem_store_description column
 * @method     array findByGemStoreBlurb(string $gem_store_blurb) Return Item objects filtered by the gem_store_blurb column
 * @method     array findByRestrictionLevel(int $restriction_level) Return Item objects filtered by the restriction_level column
 * @method     array findByRarity(int $rarity) Return Item objects filtered by the rarity column
 * @method     array findByVendorSellPrice(int $vendor_sell_price) Return Item objects filtered by the vendor_sell_price column
 * @method     array findByVendorPrice(int $vendor_price) Return Item objects filtered by the vendor_price column
 * @method     array findByKarmaPrice(int $karma_price) Return Item objects filtered by the karma_price column
 * @method     array findByImg(string $img) Return Item objects filtered by the img column
 * @method     array findByRarityWord(string $rarity_word) Return Item objects filtered by the rarity_word column
 * @method     array findByItemTypeId(int $item_type_id) Return Item objects filtered by the item_type_id column
 * @method     array findByItemSubTypeId(int $item_sub_type_id) Return Item objects filtered by the item_sub_type_id column
 * @method     array findByMaxOfferUnitPrice(int $max_offer_unit_price) Return Item objects filtered by the max_offer_unit_price column
 * @method     array findByMinSaleUnitPrice(int $min_sale_unit_price) Return Item objects filtered by the min_sale_unit_price column
 * @method     array findByOfferAvailability(int $offer_availability) Return Item objects filtered by the offer_availability column
 * @method     array findBySaleAvailability(int $sale_availability) Return Item objects filtered by the sale_availability column
 * @method     array findByGw2dbId(int $gw2db_id) Return Item objects filtered by the gw2db_id column
 * @method     array findByGw2dbExternalId(int $gw2db_external_id) Return Item objects filtered by the gw2db_external_id column
 * @method     array findByLastPriceChanged(string $last_price_changed) Return Item objects filtered by the last_price_changed column
 * @method     array findBySalePriceChangeLastHour(int $sale_price_change_last_hour) Return Item objects filtered by the sale_price_change_last_hour column
 * @method     array findByOfferPriceChangeLastHour(int $offer_price_change_last_hour) Return Item objects filtered by the offer_price_change_last_hour column
 *
 * @package    propel.generator.gw2spidy.om
 */
abstract class BaseItemQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseItemQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'gw2spidy', $modelName = 'GW2Spidy\\DB\\Item', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ItemQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     ItemQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ItemQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ItemQuery) {
            return $criteria;
        }
        $query = new ItemQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query 
     * @param     PropelPDO $con an optional connection object
     *
     * @return   Item|Item[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ItemPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return   Item A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `DATA_ID`, `TYPE_ID`, `NAME`, `GEM_STORE_DESCRIPTION`, `GEM_STORE_BLURB`, `RESTRICTION_LEVEL`, `RARITY`, `VENDOR_SELL_PRICE`, `VENDOR_PRICE`, `KARMA_PRICE`, `IMG`, `RARITY_WORD`, `ITEM_TYPE_ID`, `ITEM_SUB_TYPE_ID`, `MAX_OFFER_UNIT_PRICE`, `MIN_SALE_UNIT_PRICE`, `OFFER_AVAILABILITY`, `SALE_AVAILABILITY`, `GW2DB_ID`, `GW2DB_EXTERNAL_ID`, `LAST_PRICE_CHANGED`, `SALE_PRICE_CHANGE_LAST_HOUR`, `OFFER_PRICE_CHANGE_LAST_HOUR` FROM `item` WHERE `DATA_ID` = :p0';
        try {
            $stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new Item();
            $obj->hydrate($row);
            ItemPeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return Item|Item[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|Item[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ItemPeer::DATA_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ItemPeer::DATA_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the data_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDataId(1234); // WHERE data_id = 1234
     * $query->filterByDataId(array(12, 34)); // WHERE data_id IN (12, 34)
     * $query->filterByDataId(array('min' => 12)); // WHERE data_id > 12
     * </code>
     *
     * @param     mixed $dataId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByDataId($dataId = null, $comparison = null)
    {
        if (is_array($dataId) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(ItemPeer::DATA_ID, $dataId, $comparison);
    }

    /**
     * Filter the query on the type_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTypeId(1234); // WHERE type_id = 1234
     * $query->filterByTypeId(array(12, 34)); // WHERE type_id IN (12, 34)
     * $query->filterByTypeId(array('min' => 12)); // WHERE type_id > 12
     * </code>
     *
     * @param     mixed $typeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByTypeId($typeId = null, $comparison = null)
    {
        if (is_array($typeId)) {
            $useMinMax = false;
            if (isset($typeId['min'])) {
                $this->addUsingAlias(ItemPeer::TYPE_ID, $typeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($typeId['max'])) {
                $this->addUsingAlias(ItemPeer::TYPE_ID, $typeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::TYPE_ID, $typeId, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByName('%fooValue%'); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $name The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByName($name = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($name)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $name)) {
                $name = str_replace('*', '%', $name);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ItemPeer::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the gem_store_description column
     *
     * Example usage:
     * <code>
     * $query->filterByGemStoreDescription('fooValue');   // WHERE gem_store_description = 'fooValue'
     * $query->filterByGemStoreDescription('%fooValue%'); // WHERE gem_store_description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $gemStoreDescription The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByGemStoreDescription($gemStoreDescription = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($gemStoreDescription)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $gemStoreDescription)) {
                $gemStoreDescription = str_replace('*', '%', $gemStoreDescription);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ItemPeer::GEM_STORE_DESCRIPTION, $gemStoreDescription, $comparison);
    }

    /**
     * Filter the query on the gem_store_blurb column
     *
     * Example usage:
     * <code>
     * $query->filterByGemStoreBlurb('fooValue');   // WHERE gem_store_blurb = 'fooValue'
     * $query->filterByGemStoreBlurb('%fooValue%'); // WHERE gem_store_blurb LIKE '%fooValue%'
     * </code>
     *
     * @param     string $gemStoreBlurb The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByGemStoreBlurb($gemStoreBlurb = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($gemStoreBlurb)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $gemStoreBlurb)) {
                $gemStoreBlurb = str_replace('*', '%', $gemStoreBlurb);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ItemPeer::GEM_STORE_BLURB, $gemStoreBlurb, $comparison);
    }

    /**
     * Filter the query on the restriction_level column
     *
     * Example usage:
     * <code>
     * $query->filterByRestrictionLevel(1234); // WHERE restriction_level = 1234
     * $query->filterByRestrictionLevel(array(12, 34)); // WHERE restriction_level IN (12, 34)
     * $query->filterByRestrictionLevel(array('min' => 12)); // WHERE restriction_level > 12
     * </code>
     *
     * @param     mixed $restrictionLevel The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByRestrictionLevel($restrictionLevel = null, $comparison = null)
    {
        if (is_array($restrictionLevel)) {
            $useMinMax = false;
            if (isset($restrictionLevel['min'])) {
                $this->addUsingAlias(ItemPeer::RESTRICTION_LEVEL, $restrictionLevel['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($restrictionLevel['max'])) {
                $this->addUsingAlias(ItemPeer::RESTRICTION_LEVEL, $restrictionLevel['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::RESTRICTION_LEVEL, $restrictionLevel, $comparison);
    }

    /**
     * Filter the query on the rarity column
     *
     * Example usage:
     * <code>
     * $query->filterByRarity(1234); // WHERE rarity = 1234
     * $query->filterByRarity(array(12, 34)); // WHERE rarity IN (12, 34)
     * $query->filterByRarity(array('min' => 12)); // WHERE rarity > 12
     * </code>
     *
     * @param     mixed $rarity The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByRarity($rarity = null, $comparison = null)
    {
        if (is_array($rarity)) {
            $useMinMax = false;
            if (isset($rarity['min'])) {
                $this->addUsingAlias(ItemPeer::RARITY, $rarity['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($rarity['max'])) {
                $this->addUsingAlias(ItemPeer::RARITY, $rarity['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::RARITY, $rarity, $comparison);
    }

    /**
     * Filter the query on the vendor_sell_price column
     *
     * Example usage:
     * <code>
     * $query->filterByVendorSellPrice(1234); // WHERE vendor_sell_price = 1234
     * $query->filterByVendorSellPrice(array(12, 34)); // WHERE vendor_sell_price IN (12, 34)
     * $query->filterByVendorSellPrice(array('min' => 12)); // WHERE vendor_sell_price > 12
     * </code>
     *
     * @param     mixed $vendorSellPrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByVendorSellPrice($vendorSellPrice = null, $comparison = null)
    {
        if (is_array($vendorSellPrice)) {
            $useMinMax = false;
            if (isset($vendorSellPrice['min'])) {
                $this->addUsingAlias(ItemPeer::VENDOR_SELL_PRICE, $vendorSellPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($vendorSellPrice['max'])) {
                $this->addUsingAlias(ItemPeer::VENDOR_SELL_PRICE, $vendorSellPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::VENDOR_SELL_PRICE, $vendorSellPrice, $comparison);
    }

    /**
     * Filter the query on the vendor_price column
     *
     * Example usage:
     * <code>
     * $query->filterByVendorPrice(1234); // WHERE vendor_price = 1234
     * $query->filterByVendorPrice(array(12, 34)); // WHERE vendor_price IN (12, 34)
     * $query->filterByVendorPrice(array('min' => 12)); // WHERE vendor_price > 12
     * </code>
     *
     * @param     mixed $vendorPrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByVendorPrice($vendorPrice = null, $comparison = null)
    {
        if (is_array($vendorPrice)) {
            $useMinMax = false;
            if (isset($vendorPrice['min'])) {
                $this->addUsingAlias(ItemPeer::VENDOR_PRICE, $vendorPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($vendorPrice['max'])) {
                $this->addUsingAlias(ItemPeer::VENDOR_PRICE, $vendorPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::VENDOR_PRICE, $vendorPrice, $comparison);
    }

    /**
     * Filter the query on the karma_price column
     *
     * Example usage:
     * <code>
     * $query->filterByKarmaPrice(1234); // WHERE karma_price = 1234
     * $query->filterByKarmaPrice(array(12, 34)); // WHERE karma_price IN (12, 34)
     * $query->filterByKarmaPrice(array('min' => 12)); // WHERE karma_price > 12
     * </code>
     *
     * @param     mixed $karmaPrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByKarmaPrice($karmaPrice = null, $comparison = null)
    {
        if (is_array($karmaPrice)) {
            $useMinMax = false;
            if (isset($karmaPrice['min'])) {
                $this->addUsingAlias(ItemPeer::KARMA_PRICE, $karmaPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($karmaPrice['max'])) {
                $this->addUsingAlias(ItemPeer::KARMA_PRICE, $karmaPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::KARMA_PRICE, $karmaPrice, $comparison);
    }

    /**
     * Filter the query on the img column
     *
     * Example usage:
     * <code>
     * $query->filterByImg('fooValue');   // WHERE img = 'fooValue'
     * $query->filterByImg('%fooValue%'); // WHERE img LIKE '%fooValue%'
     * </code>
     *
     * @param     string $img The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByImg($img = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($img)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $img)) {
                $img = str_replace('*', '%', $img);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ItemPeer::IMG, $img, $comparison);
    }

    /**
     * Filter the query on the rarity_word column
     *
     * Example usage:
     * <code>
     * $query->filterByRarityWord('fooValue');   // WHERE rarity_word = 'fooValue'
     * $query->filterByRarityWord('%fooValue%'); // WHERE rarity_word LIKE '%fooValue%'
     * </code>
     *
     * @param     string $rarityWord The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByRarityWord($rarityWord = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($rarityWord)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $rarityWord)) {
                $rarityWord = str_replace('*', '%', $rarityWord);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ItemPeer::RARITY_WORD, $rarityWord, $comparison);
    }

    /**
     * Filter the query on the item_type_id column
     *
     * Example usage:
     * <code>
     * $query->filterByItemTypeId(1234); // WHERE item_type_id = 1234
     * $query->filterByItemTypeId(array(12, 34)); // WHERE item_type_id IN (12, 34)
     * $query->filterByItemTypeId(array('min' => 12)); // WHERE item_type_id > 12
     * </code>
     *
     * @see       filterByItemType()
     *
     * @param     mixed $itemTypeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByItemTypeId($itemTypeId = null, $comparison = null)
    {
        if (is_array($itemTypeId)) {
            $useMinMax = false;
            if (isset($itemTypeId['min'])) {
                $this->addUsingAlias(ItemPeer::ITEM_TYPE_ID, $itemTypeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($itemTypeId['max'])) {
                $this->addUsingAlias(ItemPeer::ITEM_TYPE_ID, $itemTypeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::ITEM_TYPE_ID, $itemTypeId, $comparison);
    }

    /**
     * Filter the query on the item_sub_type_id column
     *
     * Example usage:
     * <code>
     * $query->filterByItemSubTypeId(1234); // WHERE item_sub_type_id = 1234
     * $query->filterByItemSubTypeId(array(12, 34)); // WHERE item_sub_type_id IN (12, 34)
     * $query->filterByItemSubTypeId(array('min' => 12)); // WHERE item_sub_type_id > 12
     * </code>
     *
     * @see       filterByItemSubType()
     *
     * @param     mixed $itemSubTypeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByItemSubTypeId($itemSubTypeId = null, $comparison = null)
    {
        if (is_array($itemSubTypeId)) {
            $useMinMax = false;
            if (isset($itemSubTypeId['min'])) {
                $this->addUsingAlias(ItemPeer::ITEM_SUB_TYPE_ID, $itemSubTypeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($itemSubTypeId['max'])) {
                $this->addUsingAlias(ItemPeer::ITEM_SUB_TYPE_ID, $itemSubTypeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::ITEM_SUB_TYPE_ID, $itemSubTypeId, $comparison);
    }

    /**
     * Filter the query on the max_offer_unit_price column
     *
     * Example usage:
     * <code>
     * $query->filterByMaxOfferUnitPrice(1234); // WHERE max_offer_unit_price = 1234
     * $query->filterByMaxOfferUnitPrice(array(12, 34)); // WHERE max_offer_unit_price IN (12, 34)
     * $query->filterByMaxOfferUnitPrice(array('min' => 12)); // WHERE max_offer_unit_price > 12
     * </code>
     *
     * @param     mixed $maxOfferUnitPrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByMaxOfferUnitPrice($maxOfferUnitPrice = null, $comparison = null)
    {
        if (is_array($maxOfferUnitPrice)) {
            $useMinMax = false;
            if (isset($maxOfferUnitPrice['min'])) {
                $this->addUsingAlias(ItemPeer::MAX_OFFER_UNIT_PRICE, $maxOfferUnitPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($maxOfferUnitPrice['max'])) {
                $this->addUsingAlias(ItemPeer::MAX_OFFER_UNIT_PRICE, $maxOfferUnitPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::MAX_OFFER_UNIT_PRICE, $maxOfferUnitPrice, $comparison);
    }

    /**
     * Filter the query on the min_sale_unit_price column
     *
     * Example usage:
     * <code>
     * $query->filterByMinSaleUnitPrice(1234); // WHERE min_sale_unit_price = 1234
     * $query->filterByMinSaleUnitPrice(array(12, 34)); // WHERE min_sale_unit_price IN (12, 34)
     * $query->filterByMinSaleUnitPrice(array('min' => 12)); // WHERE min_sale_unit_price > 12
     * </code>
     *
     * @param     mixed $minSaleUnitPrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByMinSaleUnitPrice($minSaleUnitPrice = null, $comparison = null)
    {
        if (is_array($minSaleUnitPrice)) {
            $useMinMax = false;
            if (isset($minSaleUnitPrice['min'])) {
                $this->addUsingAlias(ItemPeer::MIN_SALE_UNIT_PRICE, $minSaleUnitPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($minSaleUnitPrice['max'])) {
                $this->addUsingAlias(ItemPeer::MIN_SALE_UNIT_PRICE, $minSaleUnitPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::MIN_SALE_UNIT_PRICE, $minSaleUnitPrice, $comparison);
    }

    /**
     * Filter the query on the offer_availability column
     *
     * Example usage:
     * <code>
     * $query->filterByOfferAvailability(1234); // WHERE offer_availability = 1234
     * $query->filterByOfferAvailability(array(12, 34)); // WHERE offer_availability IN (12, 34)
     * $query->filterByOfferAvailability(array('min' => 12)); // WHERE offer_availability > 12
     * </code>
     *
     * @param     mixed $offerAvailability The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByOfferAvailability($offerAvailability = null, $comparison = null)
    {
        if (is_array($offerAvailability)) {
            $useMinMax = false;
            if (isset($offerAvailability['min'])) {
                $this->addUsingAlias(ItemPeer::OFFER_AVAILABILITY, $offerAvailability['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($offerAvailability['max'])) {
                $this->addUsingAlias(ItemPeer::OFFER_AVAILABILITY, $offerAvailability['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::OFFER_AVAILABILITY, $offerAvailability, $comparison);
    }

    /**
     * Filter the query on the sale_availability column
     *
     * Example usage:
     * <code>
     * $query->filterBySaleAvailability(1234); // WHERE sale_availability = 1234
     * $query->filterBySaleAvailability(array(12, 34)); // WHERE sale_availability IN (12, 34)
     * $query->filterBySaleAvailability(array('min' => 12)); // WHERE sale_availability > 12
     * </code>
     *
     * @param     mixed $saleAvailability The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterBySaleAvailability($saleAvailability = null, $comparison = null)
    {
        if (is_array($saleAvailability)) {
            $useMinMax = false;
            if (isset($saleAvailability['min'])) {
                $this->addUsingAlias(ItemPeer::SALE_AVAILABILITY, $saleAvailability['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($saleAvailability['max'])) {
                $this->addUsingAlias(ItemPeer::SALE_AVAILABILITY, $saleAvailability['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::SALE_AVAILABILITY, $saleAvailability, $comparison);
    }

    /**
     * Filter the query on the gw2db_id column
     *
     * Example usage:
     * <code>
     * $query->filterByGw2dbId(1234); // WHERE gw2db_id = 1234
     * $query->filterByGw2dbId(array(12, 34)); // WHERE gw2db_id IN (12, 34)
     * $query->filterByGw2dbId(array('min' => 12)); // WHERE gw2db_id > 12
     * </code>
     *
     * @param     mixed $gw2dbId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByGw2dbId($gw2dbId = null, $comparison = null)
    {
        if (is_array($gw2dbId)) {
            $useMinMax = false;
            if (isset($gw2dbId['min'])) {
                $this->addUsingAlias(ItemPeer::GW2DB_ID, $gw2dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($gw2dbId['max'])) {
                $this->addUsingAlias(ItemPeer::GW2DB_ID, $gw2dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::GW2DB_ID, $gw2dbId, $comparison);
    }

    /**
     * Filter the query on the gw2db_external_id column
     *
     * Example usage:
     * <code>
     * $query->filterByGw2dbExternalId(1234); // WHERE gw2db_external_id = 1234
     * $query->filterByGw2dbExternalId(array(12, 34)); // WHERE gw2db_external_id IN (12, 34)
     * $query->filterByGw2dbExternalId(array('min' => 12)); // WHERE gw2db_external_id > 12
     * </code>
     *
     * @param     mixed $gw2dbExternalId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByGw2dbExternalId($gw2dbExternalId = null, $comparison = null)
    {
        if (is_array($gw2dbExternalId)) {
            $useMinMax = false;
            if (isset($gw2dbExternalId['min'])) {
                $this->addUsingAlias(ItemPeer::GW2DB_EXTERNAL_ID, $gw2dbExternalId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($gw2dbExternalId['max'])) {
                $this->addUsingAlias(ItemPeer::GW2DB_EXTERNAL_ID, $gw2dbExternalId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::GW2DB_EXTERNAL_ID, $gw2dbExternalId, $comparison);
    }

    /**
     * Filter the query on the last_price_changed column
     *
     * Example usage:
     * <code>
     * $query->filterByLastPriceChanged('2011-03-14'); // WHERE last_price_changed = '2011-03-14'
     * $query->filterByLastPriceChanged('now'); // WHERE last_price_changed = '2011-03-14'
     * $query->filterByLastPriceChanged(array('max' => 'yesterday')); // WHERE last_price_changed > '2011-03-13'
     * </code>
     *
     * @param     mixed $lastPriceChanged The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByLastPriceChanged($lastPriceChanged = null, $comparison = null)
    {
        if (is_array($lastPriceChanged)) {
            $useMinMax = false;
            if (isset($lastPriceChanged['min'])) {
                $this->addUsingAlias(ItemPeer::LAST_PRICE_CHANGED, $lastPriceChanged['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lastPriceChanged['max'])) {
                $this->addUsingAlias(ItemPeer::LAST_PRICE_CHANGED, $lastPriceChanged['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::LAST_PRICE_CHANGED, $lastPriceChanged, $comparison);
    }

    /**
     * Filter the query on the sale_price_change_last_hour column
     *
     * Example usage:
     * <code>
     * $query->filterBySalePriceChangeLastHour(1234); // WHERE sale_price_change_last_hour = 1234
     * $query->filterBySalePriceChangeLastHour(array(12, 34)); // WHERE sale_price_change_last_hour IN (12, 34)
     * $query->filterBySalePriceChangeLastHour(array('min' => 12)); // WHERE sale_price_change_last_hour > 12
     * </code>
     *
     * @param     mixed $salePriceChangeLastHour The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterBySalePriceChangeLastHour($salePriceChangeLastHour = null, $comparison = null)
    {
        if (is_array($salePriceChangeLastHour)) {
            $useMinMax = false;
            if (isset($salePriceChangeLastHour['min'])) {
                $this->addUsingAlias(ItemPeer::SALE_PRICE_CHANGE_LAST_HOUR, $salePriceChangeLastHour['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($salePriceChangeLastHour['max'])) {
                $this->addUsingAlias(ItemPeer::SALE_PRICE_CHANGE_LAST_HOUR, $salePriceChangeLastHour['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::SALE_PRICE_CHANGE_LAST_HOUR, $salePriceChangeLastHour, $comparison);
    }

    /**
     * Filter the query on the offer_price_change_last_hour column
     *
     * Example usage:
     * <code>
     * $query->filterByOfferPriceChangeLastHour(1234); // WHERE offer_price_change_last_hour = 1234
     * $query->filterByOfferPriceChangeLastHour(array(12, 34)); // WHERE offer_price_change_last_hour IN (12, 34)
     * $query->filterByOfferPriceChangeLastHour(array('min' => 12)); // WHERE offer_price_change_last_hour > 12
     * </code>
     *
     * @param     mixed $offerPriceChangeLastHour The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByOfferPriceChangeLastHour($offerPriceChangeLastHour = null, $comparison = null)
    {
        if (is_array($offerPriceChangeLastHour)) {
            $useMinMax = false;
            if (isset($offerPriceChangeLastHour['min'])) {
                $this->addUsingAlias(ItemPeer::OFFER_PRICE_CHANGE_LAST_HOUR, $offerPriceChangeLastHour['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($offerPriceChangeLastHour['max'])) {
                $this->addUsingAlias(ItemPeer::OFFER_PRICE_CHANGE_LAST_HOUR, $offerPriceChangeLastHour['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemPeer::OFFER_PRICE_CHANGE_LAST_HOUR, $offerPriceChangeLastHour, $comparison);
    }

    /**
     * Filter the query by a related ItemType object
     *
     * @param   ItemType|PropelObjectCollection $itemType The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   ItemQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByItemType($itemType, $comparison = null)
    {
        if ($itemType instanceof ItemType) {
            return $this
                ->addUsingAlias(ItemPeer::ITEM_TYPE_ID, $itemType->getId(), $comparison);
        } elseif ($itemType instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ItemPeer::ITEM_TYPE_ID, $itemType->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByItemType() only accepts arguments of type ItemType or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ItemType relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function joinItemType($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ItemType');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'ItemType');
        }

        return $this;
    }

    /**
     * Use the ItemType relation ItemType object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \GW2Spidy\DB\ItemTypeQuery A secondary query class using the current class as primary query
     */
    public function useItemTypeQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinItemType($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ItemType', '\GW2Spidy\DB\ItemTypeQuery');
    }

    /**
     * Filter the query by a related ItemSubType object
     *
     * @param   ItemSubType|PropelObjectCollection $itemSubType The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   ItemQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByItemSubType($itemSubType, $comparison = null)
    {
        if ($itemSubType instanceof ItemSubType) {
            return $this
                ->addUsingAlias(ItemPeer::ITEM_SUB_TYPE_ID, $itemSubType->getId(), $comparison);
        } elseif ($itemSubType instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ItemPeer::ITEM_SUB_TYPE_ID, $itemSubType->toKeyValue('Id', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByItemSubType() only accepts arguments of type ItemSubType or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ItemSubType relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function joinItemSubType($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ItemSubType');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'ItemSubType');
        }

        return $this;
    }

    /**
     * Use the ItemSubType relation ItemSubType object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \GW2Spidy\DB\ItemSubTypeQuery A secondary query class using the current class as primary query
     */
    public function useItemSubTypeQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinItemSubType($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ItemSubType', '\GW2Spidy\DB\ItemSubTypeQuery');
    }

    /**
     * Filter the query by a related Recipe object
     *
     * @param   Recipe|PropelObjectCollection $recipe  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   ItemQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByResultOfRecipe($recipe, $comparison = null)
    {
        if ($recipe instanceof Recipe) {
            return $this
                ->addUsingAlias(ItemPeer::DATA_ID, $recipe->getResultItemId(), $comparison);
        } elseif ($recipe instanceof PropelObjectCollection) {
            return $this
                ->useResultOfRecipeQuery()
                ->filterByPrimaryKeys($recipe->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByResultOfRecipe() only accepts arguments of type Recipe or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ResultOfRecipe relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function joinResultOfRecipe($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ResultOfRecipe');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'ResultOfRecipe');
        }

        return $this;
    }

    /**
     * Use the ResultOfRecipe relation Recipe object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \GW2Spidy\DB\RecipeQuery A secondary query class using the current class as primary query
     */
    public function useResultOfRecipeQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinResultOfRecipe($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ResultOfRecipe', '\GW2Spidy\DB\RecipeQuery');
    }

    /**
     * Filter the query by a related RecipeIngredient object
     *
     * @param   RecipeIngredient|PropelObjectCollection $recipeIngredient  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   ItemQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByIngredient($recipeIngredient, $comparison = null)
    {
        if ($recipeIngredient instanceof RecipeIngredient) {
            return $this
                ->addUsingAlias(ItemPeer::DATA_ID, $recipeIngredient->getItemId(), $comparison);
        } elseif ($recipeIngredient instanceof PropelObjectCollection) {
            return $this
                ->useIngredientQuery()
                ->filterByPrimaryKeys($recipeIngredient->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByIngredient() only accepts arguments of type RecipeIngredient or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Ingredient relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function joinIngredient($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Ingredient');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Ingredient');
        }

        return $this;
    }

    /**
     * Use the Ingredient relation RecipeIngredient object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \GW2Spidy\DB\RecipeIngredientQuery A secondary query class using the current class as primary query
     */
    public function useIngredientQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinIngredient($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Ingredient', '\GW2Spidy\DB\RecipeIngredientQuery');
    }

    /**
     * Filter the query by a related SellListing object
     *
     * @param   SellListing|PropelObjectCollection $sellListing  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   ItemQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterBySellListing($sellListing, $comparison = null)
    {
        if ($sellListing instanceof SellListing) {
            return $this
                ->addUsingAlias(ItemPeer::DATA_ID, $sellListing->getItemId(), $comparison);
        } elseif ($sellListing instanceof PropelObjectCollection) {
            return $this
                ->useSellListingQuery()
                ->filterByPrimaryKeys($sellListing->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySellListing() only accepts arguments of type SellListing or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SellListing relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function joinSellListing($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SellListing');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'SellListing');
        }

        return $this;
    }

    /**
     * Use the SellListing relation SellListing object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \GW2Spidy\DB\SellListingQuery A secondary query class using the current class as primary query
     */
    public function useSellListingQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSellListing($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SellListing', '\GW2Spidy\DB\SellListingQuery');
    }

    /**
     * Filter the query by a related BuyListing object
     *
     * @param   BuyListing|PropelObjectCollection $buyListing  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   ItemQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByBuyListing($buyListing, $comparison = null)
    {
        if ($buyListing instanceof BuyListing) {
            return $this
                ->addUsingAlias(ItemPeer::DATA_ID, $buyListing->getItemId(), $comparison);
        } elseif ($buyListing instanceof PropelObjectCollection) {
            return $this
                ->useBuyListingQuery()
                ->filterByPrimaryKeys($buyListing->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByBuyListing() only accepts arguments of type BuyListing or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the BuyListing relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function joinBuyListing($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('BuyListing');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'BuyListing');
        }

        return $this;
    }

    /**
     * Use the BuyListing relation BuyListing object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \GW2Spidy\DB\BuyListingQuery A secondary query class using the current class as primary query
     */
    public function useBuyListingQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinBuyListing($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'BuyListing', '\GW2Spidy\DB\BuyListingQuery');
    }

    /**
     * Filter the query by a related Watchlist object
     *
     * @param   Watchlist|PropelObjectCollection $watchlist  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   ItemQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByOnWatchlist($watchlist, $comparison = null)
    {
        if ($watchlist instanceof Watchlist) {
            return $this
                ->addUsingAlias(ItemPeer::DATA_ID, $watchlist->getItemId(), $comparison);
        } elseif ($watchlist instanceof PropelObjectCollection) {
            return $this
                ->useOnWatchlistQuery()
                ->filterByPrimaryKeys($watchlist->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByOnWatchlist() only accepts arguments of type Watchlist or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OnWatchlist relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function joinOnWatchlist($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OnWatchlist');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'OnWatchlist');
        }

        return $this;
    }

    /**
     * Use the OnWatchlist relation Watchlist object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \GW2Spidy\DB\WatchlistQuery A secondary query class using the current class as primary query
     */
    public function useOnWatchlistQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOnWatchlist($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OnWatchlist', '\GW2Spidy\DB\WatchlistQuery');
    }

    /**
     * Filter the query by a related Recipe object
     * using the recipe_ingredient table as cross reference
     *
     * @param   Recipe $recipe the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   ItemQuery The current query, for fluid interface
     */
    public function filterByRecipe($recipe, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useIngredientQuery()
            ->filterByRecipe($recipe, $comparison)
            ->endUse();
    }

    /**
     * Filter the query by a related User object
     * using the watchlist table as cross reference
     *
     * @param   User $user the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   ItemQuery The current query, for fluid interface
     */
    public function filterByUser($user, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useOnWatchlistQuery()
            ->filterByUser($user, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   Item $item Object to remove from the list of results
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function prune($item = null)
    {
        if ($item) {
            $this->addUsingAlias(ItemPeer::DATA_ID, $item->getDataId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseItemQuery