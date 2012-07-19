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
use GW2Spidy\DB\Item;
use GW2Spidy\DB\Listing;
use GW2Spidy\DB\ListingPeer;
use GW2Spidy\DB\ListingQuery;

/**
 * Base class that represents a query for the 'listing' table.
 *
 * 
 *
 * @method     ListingQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ListingQuery orderByListingDate($order = Criteria::ASC) Order by the listing_date column
 * @method     ListingQuery orderByListingTime($order = Criteria::ASC) Order by the listing_time column
 * @method     ListingQuery orderByItemId($order = Criteria::ASC) Order by the item_id column
 * @method     ListingQuery orderByListings($order = Criteria::ASC) Order by the listings column
 * @method     ListingQuery orderByUnitPrice($order = Criteria::ASC) Order by the unit_price column
 * @method     ListingQuery orderByQuantity($order = Criteria::ASC) Order by the quantity column
 *
 * @method     ListingQuery groupById() Group by the id column
 * @method     ListingQuery groupByListingDate() Group by the listing_date column
 * @method     ListingQuery groupByListingTime() Group by the listing_time column
 * @method     ListingQuery groupByItemId() Group by the item_id column
 * @method     ListingQuery groupByListings() Group by the listings column
 * @method     ListingQuery groupByUnitPrice() Group by the unit_price column
 * @method     ListingQuery groupByQuantity() Group by the quantity column
 *
 * @method     ListingQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ListingQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ListingQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ListingQuery leftJoinItem($relationAlias = null) Adds a LEFT JOIN clause to the query using the Item relation
 * @method     ListingQuery rightJoinItem($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Item relation
 * @method     ListingQuery innerJoinItem($relationAlias = null) Adds a INNER JOIN clause to the query using the Item relation
 *
 * @method     Listing findOne(PropelPDO $con = null) Return the first Listing matching the query
 * @method     Listing findOneOrCreate(PropelPDO $con = null) Return the first Listing matching the query, or a new Listing object populated from the query conditions when no match is found
 *
 * @method     Listing findOneById(int $id) Return the first Listing filtered by the id column
 * @method     Listing findOneByListingDate(string $listing_date) Return the first Listing filtered by the listing_date column
 * @method     Listing findOneByListingTime(string $listing_time) Return the first Listing filtered by the listing_time column
 * @method     Listing findOneByItemId(int $item_id) Return the first Listing filtered by the item_id column
 * @method     Listing findOneByListings(int $listings) Return the first Listing filtered by the listings column
 * @method     Listing findOneByUnitPrice(int $unit_price) Return the first Listing filtered by the unit_price column
 * @method     Listing findOneByQuantity(int $quantity) Return the first Listing filtered by the quantity column
 *
 * @method     array findById(int $id) Return Listing objects filtered by the id column
 * @method     array findByListingDate(string $listing_date) Return Listing objects filtered by the listing_date column
 * @method     array findByListingTime(string $listing_time) Return Listing objects filtered by the listing_time column
 * @method     array findByItemId(int $item_id) Return Listing objects filtered by the item_id column
 * @method     array findByListings(int $listings) Return Listing objects filtered by the listings column
 * @method     array findByUnitPrice(int $unit_price) Return Listing objects filtered by the unit_price column
 * @method     array findByQuantity(int $quantity) Return Listing objects filtered by the quantity column
 *
 * @package    propel.generator.gw2spidy.om
 */
abstract class BaseListingQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseListingQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'gw2spidy', $modelName = 'GW2Spidy\\DB\\Listing', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ListingQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     ListingQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ListingQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ListingQuery) {
            return $criteria;
        }
        $query = new ListingQuery();
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
     * @return   Listing|Listing[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ListingPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ListingPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   Listing A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `ID`, `LISTING_DATE`, `LISTING_TIME`, `ITEM_ID`, `LISTINGS`, `UNIT_PRICE`, `QUANTITY` FROM `listing` WHERE `ID` = :p0';
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
            $obj = new Listing();
            $obj->hydrate($row);
            ListingPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Listing|Listing[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Listing[]|mixed the list of results, formatted by the current formatter
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
     * @return ListingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ListingPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ListingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ListingPeer::ID, $keys, Criteria::IN);
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
     * @return ListingQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(ListingPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the listing_date column
     *
     * Example usage:
     * <code>
     * $query->filterByListingDate('2011-03-14'); // WHERE listing_date = '2011-03-14'
     * $query->filterByListingDate('now'); // WHERE listing_date = '2011-03-14'
     * $query->filterByListingDate(array('max' => 'yesterday')); // WHERE listing_date > '2011-03-13'
     * </code>
     *
     * @param     mixed $listingDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListingQuery The current query, for fluid interface
     */
    public function filterByListingDate($listingDate = null, $comparison = null)
    {
        if (is_array($listingDate)) {
            $useMinMax = false;
            if (isset($listingDate['min'])) {
                $this->addUsingAlias(ListingPeer::LISTING_DATE, $listingDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($listingDate['max'])) {
                $this->addUsingAlias(ListingPeer::LISTING_DATE, $listingDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ListingPeer::LISTING_DATE, $listingDate, $comparison);
    }

    /**
     * Filter the query on the listing_time column
     *
     * Example usage:
     * <code>
     * $query->filterByListingTime('2011-03-14'); // WHERE listing_time = '2011-03-14'
     * $query->filterByListingTime('now'); // WHERE listing_time = '2011-03-14'
     * $query->filterByListingTime(array('max' => 'yesterday')); // WHERE listing_time > '2011-03-13'
     * </code>
     *
     * @param     mixed $listingTime The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListingQuery The current query, for fluid interface
     */
    public function filterByListingTime($listingTime = null, $comparison = null)
    {
        if (is_array($listingTime)) {
            $useMinMax = false;
            if (isset($listingTime['min'])) {
                $this->addUsingAlias(ListingPeer::LISTING_TIME, $listingTime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($listingTime['max'])) {
                $this->addUsingAlias(ListingPeer::LISTING_TIME, $listingTime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ListingPeer::LISTING_TIME, $listingTime, $comparison);
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
     * @return ListingQuery The current query, for fluid interface
     */
    public function filterByItemId($itemId = null, $comparison = null)
    {
        if (is_array($itemId)) {
            $useMinMax = false;
            if (isset($itemId['min'])) {
                $this->addUsingAlias(ListingPeer::ITEM_ID, $itemId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($itemId['max'])) {
                $this->addUsingAlias(ListingPeer::ITEM_ID, $itemId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ListingPeer::ITEM_ID, $itemId, $comparison);
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
     * @return ListingQuery The current query, for fluid interface
     */
    public function filterByListings($listings = null, $comparison = null)
    {
        if (is_array($listings)) {
            $useMinMax = false;
            if (isset($listings['min'])) {
                $this->addUsingAlias(ListingPeer::LISTINGS, $listings['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($listings['max'])) {
                $this->addUsingAlias(ListingPeer::LISTINGS, $listings['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ListingPeer::LISTINGS, $listings, $comparison);
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
     * @return ListingQuery The current query, for fluid interface
     */
    public function filterByUnitPrice($unitPrice = null, $comparison = null)
    {
        if (is_array($unitPrice)) {
            $useMinMax = false;
            if (isset($unitPrice['min'])) {
                $this->addUsingAlias(ListingPeer::UNIT_PRICE, $unitPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($unitPrice['max'])) {
                $this->addUsingAlias(ListingPeer::UNIT_PRICE, $unitPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ListingPeer::UNIT_PRICE, $unitPrice, $comparison);
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
     * @return ListingQuery The current query, for fluid interface
     */
    public function filterByQuantity($quantity = null, $comparison = null)
    {
        if (is_array($quantity)) {
            $useMinMax = false;
            if (isset($quantity['min'])) {
                $this->addUsingAlias(ListingPeer::QUANTITY, $quantity['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($quantity['max'])) {
                $this->addUsingAlias(ListingPeer::QUANTITY, $quantity['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ListingPeer::QUANTITY, $quantity, $comparison);
    }

    /**
     * Filter the query by a related Item object
     *
     * @param   Item|PropelObjectCollection $item The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   ListingQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByItem($item, $comparison = null)
    {
        if ($item instanceof Item) {
            return $this
                ->addUsingAlias(ListingPeer::ITEM_ID, $item->getDataId(), $comparison);
        } elseif ($item instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ListingPeer::ITEM_ID, $item->toKeyValue('PrimaryKey', 'DataId'), $comparison);
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
     * @return ListingQuery The current query, for fluid interface
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
     * @param   Listing $listing Object to remove from the list of results
     *
     * @return ListingQuery The current query, for fluid interface
     */
    public function prune($listing = null)
    {
        if ($listing) {
            $this->addUsingAlias(ListingPeer::ID, $listing->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseListingQuery