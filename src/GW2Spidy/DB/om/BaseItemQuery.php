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
use GW2Spidy\DB\SellListing;

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
 * @method     ItemQuery orderByImg($order = Criteria::ASC) Order by the img column
 * @method     ItemQuery orderByRarityWord($order = Criteria::ASC) Order by the rarity_word column
 * @method     ItemQuery orderByItemTypeId($order = Criteria::ASC) Order by the item_type_id column
 * @method     ItemQuery orderByItemSubTypeId($order = Criteria::ASC) Order by the item_sub_type_id column
 *
 * @method     ItemQuery groupByDataId() Group by the data_id column
 * @method     ItemQuery groupByTypeId() Group by the type_id column
 * @method     ItemQuery groupByName() Group by the name column
 * @method     ItemQuery groupByGemStoreDescription() Group by the gem_store_description column
 * @method     ItemQuery groupByGemStoreBlurb() Group by the gem_store_blurb column
 * @method     ItemQuery groupByRestrictionLevel() Group by the restriction_level column
 * @method     ItemQuery groupByRarity() Group by the rarity column
 * @method     ItemQuery groupByVendorSellPrice() Group by the vendor_sell_price column
 * @method     ItemQuery groupByImg() Group by the img column
 * @method     ItemQuery groupByRarityWord() Group by the rarity_word column
 * @method     ItemQuery groupByItemTypeId() Group by the item_type_id column
 * @method     ItemQuery groupByItemSubTypeId() Group by the item_sub_type_id column
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
 * @method     ItemQuery leftJoinSellListing($relationAlias = null) Adds a LEFT JOIN clause to the query using the SellListing relation
 * @method     ItemQuery rightJoinSellListing($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SellListing relation
 * @method     ItemQuery innerJoinSellListing($relationAlias = null) Adds a INNER JOIN clause to the query using the SellListing relation
 *
 * @method     ItemQuery leftJoinBuyListing($relationAlias = null) Adds a LEFT JOIN clause to the query using the BuyListing relation
 * @method     ItemQuery rightJoinBuyListing($relationAlias = null) Adds a RIGHT JOIN clause to the query using the BuyListing relation
 * @method     ItemQuery innerJoinBuyListing($relationAlias = null) Adds a INNER JOIN clause to the query using the BuyListing relation
 *
 * @method     Item findOne(PropelPDO $con = null) Return the first Item matching the query
 * @method     Item findOneOrCreate(PropelPDO $con = null) Return the first Item matching the query, or a new Item object populated from the query conditions when no match is found
 *
 * @method     Item findOneByDataId(int $data_id) Return the first Item filtered by the data_id column
 * @method     Item findOneByTypeId(int $type_id) Return the first Item filtered by the type_id column
 * @method     Item findOneByName(string $name) Return the first Item filtered by the name column
 * @method     Item findOneByGemStoreDescription(string $gem_store_description) Return the first Item filtered by the gem_store_description column
 * @method     Item findOneByGemStoreBlurb(string $gem_store_blurb) Return the first Item filtered by the gem_store_blurb column
 * @method     Item findOneByRestrictionLevel(string $restriction_level) Return the first Item filtered by the restriction_level column
 * @method     Item findOneByRarity(string $rarity) Return the first Item filtered by the rarity column
 * @method     Item findOneByVendorSellPrice(string $vendor_sell_price) Return the first Item filtered by the vendor_sell_price column
 * @method     Item findOneByImg(string $img) Return the first Item filtered by the img column
 * @method     Item findOneByRarityWord(string $rarity_word) Return the first Item filtered by the rarity_word column
 * @method     Item findOneByItemTypeId(int $item_type_id) Return the first Item filtered by the item_type_id column
 * @method     Item findOneByItemSubTypeId(int $item_sub_type_id) Return the first Item filtered by the item_sub_type_id column
 *
 * @method     array findByDataId(int $data_id) Return Item objects filtered by the data_id column
 * @method     array findByTypeId(int $type_id) Return Item objects filtered by the type_id column
 * @method     array findByName(string $name) Return Item objects filtered by the name column
 * @method     array findByGemStoreDescription(string $gem_store_description) Return Item objects filtered by the gem_store_description column
 * @method     array findByGemStoreBlurb(string $gem_store_blurb) Return Item objects filtered by the gem_store_blurb column
 * @method     array findByRestrictionLevel(string $restriction_level) Return Item objects filtered by the restriction_level column
 * @method     array findByRarity(string $rarity) Return Item objects filtered by the rarity column
 * @method     array findByVendorSellPrice(string $vendor_sell_price) Return Item objects filtered by the vendor_sell_price column
 * @method     array findByImg(string $img) Return Item objects filtered by the img column
 * @method     array findByRarityWord(string $rarity_word) Return Item objects filtered by the rarity_word column
 * @method     array findByItemTypeId(int $item_type_id) Return Item objects filtered by the item_type_id column
 * @method     array findByItemSubTypeId(int $item_sub_type_id) Return Item objects filtered by the item_sub_type_id column
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
        $sql = 'SELECT `DATA_ID`, `TYPE_ID`, `NAME`, `GEM_STORE_DESCRIPTION`, `GEM_STORE_BLURB`, `RESTRICTION_LEVEL`, `RARITY`, `VENDOR_SELL_PRICE`, `IMG`, `RARITY_WORD`, `ITEM_TYPE_ID`, `ITEM_SUB_TYPE_ID` FROM `item` WHERE `DATA_ID` = :p0';
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
     * $query->filterByRestrictionLevel('fooValue');   // WHERE restriction_level = 'fooValue'
     * $query->filterByRestrictionLevel('%fooValue%'); // WHERE restriction_level LIKE '%fooValue%'
     * </code>
     *
     * @param     string $restrictionLevel The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByRestrictionLevel($restrictionLevel = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($restrictionLevel)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $restrictionLevel)) {
                $restrictionLevel = str_replace('*', '%', $restrictionLevel);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ItemPeer::RESTRICTION_LEVEL, $restrictionLevel, $comparison);
    }

    /**
     * Filter the query on the rarity column
     *
     * Example usage:
     * <code>
     * $query->filterByRarity('fooValue');   // WHERE rarity = 'fooValue'
     * $query->filterByRarity('%fooValue%'); // WHERE rarity LIKE '%fooValue%'
     * </code>
     *
     * @param     string $rarity The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByRarity($rarity = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($rarity)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $rarity)) {
                $rarity = str_replace('*', '%', $rarity);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ItemPeer::RARITY, $rarity, $comparison);
    }

    /**
     * Filter the query on the vendor_sell_price column
     *
     * Example usage:
     * <code>
     * $query->filterByVendorSellPrice('fooValue');   // WHERE vendor_sell_price = 'fooValue'
     * $query->filterByVendorSellPrice('%fooValue%'); // WHERE vendor_sell_price LIKE '%fooValue%'
     * </code>
     *
     * @param     string $vendorSellPrice The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemQuery The current query, for fluid interface
     */
    public function filterByVendorSellPrice($vendorSellPrice = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($vendorSellPrice)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $vendorSellPrice)) {
                $vendorSellPrice = str_replace('*', '%', $vendorSellPrice);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ItemPeer::VENDOR_SELL_PRICE, $vendorSellPrice, $comparison);
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
    public function joinItemType($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
    public function useItemTypeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
    public function joinItemSubType($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
    public function useItemSubTypeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinItemSubType($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ItemSubType', '\GW2Spidy\DB\ItemSubTypeQuery');
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