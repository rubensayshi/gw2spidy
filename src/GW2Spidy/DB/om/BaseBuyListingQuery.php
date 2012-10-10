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
use GW2Spidy\DB\BuyListingPeer;
use GW2Spidy\DB\BuyListingQuery;
use GW2Spidy\DB\Item;

/**
 * Base class that represents a query for the 'buy_listing' table.
 *
 * 
 *
 * @method     BuyListingQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     BuyListingQuery orderByListingDatetime($order = Criteria::ASC) Order by the listing_datetime column
 * @method     BuyListingQuery orderByItemId($order = Criteria::ASC) Order by the item_id column
 * @method     BuyListingQuery orderByListings($order = Criteria::ASC) Order by the listings column
 * @method     BuyListingQuery orderByUnitPrice($order = Criteria::ASC) Order by the unit_price column
 * @method     BuyListingQuery orderByQuantity($order = Criteria::ASC) Order by the quantity column
 *
 * @method     BuyListingQuery groupById() Group by the id column
 * @method     BuyListingQuery groupByListingDatetime() Group by the listing_datetime column
 * @method     BuyListingQuery groupByItemId() Group by the item_id column
 * @method     BuyListingQuery groupByListings() Group by the listings column
 * @method     BuyListingQuery groupByUnitPrice() Group by the unit_price column
 * @method     BuyListingQuery groupByQuantity() Group by the quantity column
 *
 * @method     BuyListingQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     BuyListingQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     BuyListingQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     BuyListingQuery leftJoinItem($relationAlias = null) Adds a LEFT JOIN clause to the query using the Item relation
 * @method     BuyListingQuery rightJoinItem($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Item relation
 * @method     BuyListingQuery innerJoinItem($relationAlias = null) Adds a INNER JOIN clause to the query using the Item relation
 *
 * @method     BuyListing findOne(PropelPDO $con = null) Return the first BuyListing matching the query
 * @method     BuyListing findOneOrCreate(PropelPDO $con = null) Return the first BuyListing matching the query, or a new BuyListing object populated from the query conditions when no match is found
 *
 * @method     BuyListing findOneById(int $id) Return the first BuyListing filtered by the id column
 * @method     BuyListing findOneByListingDatetime(string $listing_datetime) Return the first BuyListing filtered by the listing_datetime column
 * @method     BuyListing findOneByItemId(int $item_id) Return the first BuyListing filtered by the item_id column
 * @method     BuyListing findOneByListings(int $listings) Return the first BuyListing filtered by the listings column
 * @method     BuyListing findOneByUnitPrice(int $unit_price) Return the first BuyListing filtered by the unit_price column
 * @method     BuyListing findOneByQuantity(int $quantity) Return the first BuyListing filtered by the quantity column
 *
 * @method     array findById(int $id) Return BuyListing objects filtered by the id column
 * @method     array findByListingDatetime(string $listing_datetime) Return BuyListing objects filtered by the listing_datetime column
 * @method     array findByItemId(int $item_id) Return BuyListing objects filtered by the item_id column
 * @method     array findByListings(int $listings) Return BuyListing objects filtered by the listings column
 * @method     array findByUnitPrice(int $unit_price) Return BuyListing objects filtered by the unit_price column
 * @method     array findByQuantity(int $quantity) Return BuyListing objects filtered by the quantity column
 *
 * @package    propel.generator.gw2spidy.om
 */
abstract class BaseBuyListingQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseBuyListingQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'gw2spidy', $modelName = 'GW2Spidy\\DB\\BuyListing', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new BuyListingQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     BuyListingQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return BuyListingQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof BuyListingQuery) {
            return $criteria;
        }
        $query = new BuyListingQuery();
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
     * @return   BuyListing|BuyListing[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = BuyListingPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(BuyListingPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   BuyListing A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `ID`, `LISTING_DATETIME`, `ITEM_ID`, `LISTINGS`, `UNIT_PRICE`, `QUANTITY` FROM `buy_listing` WHERE `ID` = :p0';
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
            $obj = new BuyListing();
            $obj->hydrate($row);
            BuyListingPeer::addInstanceToPool($obj, (string) $key);
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
     * @return BuyListing|BuyListing[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|BuyListing[]|mixed the list of results, formatted by the current formatter
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
     * @return BuyListingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(BuyListingPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return BuyListingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(BuyListingPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return BuyListingQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(BuyListingPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the listing_datetime column
     *
     * Example usage:
     * <code>
     * $query->filterByListingDatetime('2011-03-14'); // WHERE listing_datetime = '2011-03-14'
     * $query->filterByListingDatetime('now'); // WHERE listing_datetime = '2011-03-14'
     * $query->filterByListingDatetime(array('max' => 'yesterday')); // WHERE listing_datetime > '2011-03-13'
     * </code>
     *
     * @param     mixed $listingDatetime The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return BuyListingQuery The current query, for fluid interface
     */
    public function filterByListingDatetime($listingDatetime = null, $comparison = null)
    {
        if (is_array($listingDatetime)) {
            $useMinMax = false;
            if (isset($listingDatetime['min'])) {
                $this->addUsingAlias(BuyListingPeer::LISTING_DATETIME, $listingDatetime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($listingDatetime['max'])) {
                $this->addUsingAlias(BuyListingPeer::LISTING_DATETIME, $listingDatetime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BuyListingPeer::LISTING_DATETIME, $listingDatetime, $comparison);
    }

    /**
     * Filter the query on the item_id column
     *
     * Example usage:
     * <code>
     * $query->filterByItemId(1234); // WHERE item_id = 1234
     * $query->filterByItemId(array(12, 34)); // WHERE item_id IN (12, 34)
     * $query->filterByItemId(array('min' => 12)); // WHERE item_id > 12
     * </code>
     *
     * @see       filterByItem()
     *
     * @param     mixed $itemId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return BuyListingQuery The current query, for fluid interface
     */
    public function filterByItemId($itemId = null, $comparison = null)
    {
        if (is_array($itemId)) {
            $useMinMax = false;
            if (isset($itemId['min'])) {
                $this->addUsingAlias(BuyListingPeer::ITEM_ID, $itemId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($itemId['max'])) {
                $this->addUsingAlias(BuyListingPeer::ITEM_ID, $itemId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BuyListingPeer::ITEM_ID, $itemId, $comparison);
    }

    /**
     * Filter the query on the listings column
     *
     * Example usage:
     * <code>
     * $query->filterByListings(1234); // WHERE listings = 1234
     * $query->filterByListings(array(12, 34)); // WHERE listings IN (12, 34)
     * $query->filterByListings(array('min' => 12)); // WHERE listings > 12
     * </code>
     *
     * @param     mixed $listings The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return BuyListingQuery The current query, for fluid interface
     */
    public function filterByListings($listings = null, $comparison = null)
    {
        if (is_array($listings)) {
            $useMinMax = false;
            if (isset($listings['min'])) {
                $this->addUsingAlias(BuyListingPeer::LISTINGS, $listings['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($listings['max'])) {
                $this->addUsingAlias(BuyListingPeer::LISTINGS, $listings['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BuyListingPeer::LISTINGS, $listings, $comparison);
    }

    /**
     * Filter the query on the unit_price column
     *
     * Example usage:
     * <code>
     * $query->filterByUnitPrice(1234); // WHERE unit_price = 1234
     * $query->filterByUnitPrice(array(12, 34)); // WHERE unit_price IN (12, 34)
     * $query->filterByUnitPrice(array('min' => 12)); // WHERE unit_price > 12
     * </code>
     *
     * @param     mixed $unitPrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return BuyListingQuery The current query, for fluid interface
     */
    public function filterByUnitPrice($unitPrice = null, $comparison = null)
    {
        if (is_array($unitPrice)) {
            $useMinMax = false;
            if (isset($unitPrice['min'])) {
                $this->addUsingAlias(BuyListingPeer::UNIT_PRICE, $unitPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($unitPrice['max'])) {
                $this->addUsingAlias(BuyListingPeer::UNIT_PRICE, $unitPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BuyListingPeer::UNIT_PRICE, $unitPrice, $comparison);
    }

    /**
     * Filter the query on the quantity column
     *
     * Example usage:
     * <code>
     * $query->filterByQuantity(1234); // WHERE quantity = 1234
     * $query->filterByQuantity(array(12, 34)); // WHERE quantity IN (12, 34)
     * $query->filterByQuantity(array('min' => 12)); // WHERE quantity > 12
     * </code>
     *
     * @param     mixed $quantity The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return BuyListingQuery The current query, for fluid interface
     */
    public function filterByQuantity($quantity = null, $comparison = null)
    {
        if (is_array($quantity)) {
            $useMinMax = false;
            if (isset($quantity['min'])) {
                $this->addUsingAlias(BuyListingPeer::QUANTITY, $quantity['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($quantity['max'])) {
                $this->addUsingAlias(BuyListingPeer::QUANTITY, $quantity['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BuyListingPeer::QUANTITY, $quantity, $comparison);
    }

    /**
     * Filter the query by a related Item object
     *
     * @param   Item|PropelObjectCollection $item The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   BuyListingQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByItem($item, $comparison = null)
    {
        if ($item instanceof Item) {
            return $this
                ->addUsingAlias(BuyListingPeer::ITEM_ID, $item->getDataId(), $comparison);
        } elseif ($item instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(BuyListingPeer::ITEM_ID, $item->toKeyValue('PrimaryKey', 'DataId'), $comparison);
        } else {
            throw new PropelException('filterByItem() only accepts arguments of type Item or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Item relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return BuyListingQuery The current query, for fluid interface
     */
    public function joinItem($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Item');

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
            $this->addJoinObject($join, 'Item');
        }

        return $this;
    }

    /**
     * Use the Item relation Item object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \GW2Spidy\DB\ItemQuery A secondary query class using the current class as primary query
     */
    public function useItemQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinItem($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Item', '\GW2Spidy\DB\ItemQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   BuyListing $buyListing Object to remove from the list of results
     *
     * @return BuyListingQuery The current query, for fluid interface
     */
    public function prune($buyListing = null)
    {
        if ($buyListing) {
            $this->addUsingAlias(BuyListingPeer::ID, $buyListing->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseBuyListingQuery