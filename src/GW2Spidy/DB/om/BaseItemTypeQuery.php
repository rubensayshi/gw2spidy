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
use GW2Spidy\DB\ItemSubType;
use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\ItemTypePeer;
use GW2Spidy\DB\ItemTypeQuery;

/**
 * Base class that represents a query for the 'item_type' table.
 *
 * 
 *
 * @method     ItemTypeQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ItemTypeQuery orderByTitle($order = Criteria::ASC) Order by the title column
 *
 * @method     ItemTypeQuery groupById() Group by the id column
 * @method     ItemTypeQuery groupByTitle() Group by the title column
 *
 * @method     ItemTypeQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ItemTypeQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ItemTypeQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ItemTypeQuery leftJoinSubType($relationAlias = null) Adds a LEFT JOIN clause to the query using the SubType relation
 * @method     ItemTypeQuery rightJoinSubType($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SubType relation
 * @method     ItemTypeQuery innerJoinSubType($relationAlias = null) Adds a INNER JOIN clause to the query using the SubType relation
 *
 * @method     ItemTypeQuery leftJoinItem($relationAlias = null) Adds a LEFT JOIN clause to the query using the Item relation
 * @method     ItemTypeQuery rightJoinItem($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Item relation
 * @method     ItemTypeQuery innerJoinItem($relationAlias = null) Adds a INNER JOIN clause to the query using the Item relation
 *
 * @method     ItemType findOne(PropelPDO $con = null) Return the first ItemType matching the query
 * @method     ItemType findOneOrCreate(PropelPDO $con = null) Return the first ItemType matching the query, or a new ItemType object populated from the query conditions when no match is found
 *
 * @method     ItemType findOneById(int $id) Return the first ItemType filtered by the id column
 * @method     ItemType findOneByTitle(string $title) Return the first ItemType filtered by the title column
 *
 * @method     array findById(int $id) Return ItemType objects filtered by the id column
 * @method     array findByTitle(string $title) Return ItemType objects filtered by the title column
 *
 * @package    propel.generator.gw2spidy.om
 */
abstract class BaseItemTypeQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseItemTypeQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'gw2spidy', $modelName = 'GW2Spidy\\DB\\ItemType', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ItemTypeQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     ItemTypeQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ItemTypeQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ItemTypeQuery) {
            return $criteria;
        }
        $query = new ItemTypeQuery();
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
     * @return   ItemType|ItemType[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ItemTypePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ItemTypePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   ItemType A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `ID`, `TITLE` FROM `item_type` WHERE `ID` = :p0';
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
            $obj = new ItemType();
            $obj->hydrate($row);
            ItemTypePeer::addInstanceToPool($obj, (string) $key);
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
     * @return ItemType|ItemType[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|ItemType[]|mixed the list of results, formatted by the current formatter
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
     * @return ItemTypeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ItemTypePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ItemTypeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ItemTypePeer::ID, $keys, Criteria::IN);
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
     * @return ItemTypeQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(ItemTypePeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the title column
     *
     * Example usage:
     * <code>
     * $query->filterByTitle('fooValue');   // WHERE title = 'fooValue'
     * $query->filterByTitle('%fooValue%'); // WHERE title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $title The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemTypeQuery The current query, for fluid interface
     */
    public function filterByTitle($title = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($title)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $title)) {
                $title = str_replace('*', '%', $title);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ItemTypePeer::TITLE, $title, $comparison);
    }

    /**
     * Filter the query by a related ItemSubType object
     *
     * @param   ItemSubType|PropelObjectCollection $itemSubType  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   ItemTypeQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterBySubType($itemSubType, $comparison = null)
    {
        if ($itemSubType instanceof ItemSubType) {
            return $this
                ->addUsingAlias(ItemTypePeer::ID, $itemSubType->getMainTypeId(), $comparison);
        } elseif ($itemSubType instanceof PropelObjectCollection) {
            return $this
                ->useSubTypeQuery()
                ->filterByPrimaryKeys($itemSubType->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySubType() only accepts arguments of type ItemSubType or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SubType relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ItemTypeQuery The current query, for fluid interface
     */
    public function joinSubType($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SubType');

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
            $this->addJoinObject($join, 'SubType');
        }

        return $this;
    }

    /**
     * Use the SubType relation ItemSubType object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \GW2Spidy\DB\ItemSubTypeQuery A secondary query class using the current class as primary query
     */
    public function useSubTypeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSubType($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SubType', '\GW2Spidy\DB\ItemSubTypeQuery');
    }

    /**
     * Filter the query by a related Item object
     *
     * @param   Item|PropelObjectCollection $item  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   ItemTypeQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByItem($item, $comparison = null)
    {
        if ($item instanceof Item) {
            return $this
                ->addUsingAlias(ItemTypePeer::ID, $item->getItemTypeId(), $comparison);
        } elseif ($item instanceof PropelObjectCollection) {
            return $this
                ->useItemQuery()
                ->filterByPrimaryKeys($item->getPrimaryKeys())
                ->endUse();
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
     * @return ItemTypeQuery The current query, for fluid interface
     */
    public function joinItem($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useItemQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinItem($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Item', '\GW2Spidy\DB\ItemQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ItemType $itemType Object to remove from the list of results
     *
     * @return ItemTypeQuery The current query, for fluid interface
     */
    public function prune($itemType = null)
    {
        if ($itemType) {
            $this->addUsingAlias(ItemTypePeer::ID, $itemType->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseItemTypeQuery