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
use GW2Spidy\DB\ItemPeer;
use GW2Spidy\DB\ItemQuery;
use \GW2Spidy\DB\ItemType;

/**
 * Base class that represents a query for the 'item' table.
 *
 * 
 *
 * @method     ItemQuery orderByDataId($order = Criteria::ASC) Order by the data_id column
 * @method     ItemQuery orderByTypeId($order = Criteria::ASC) Order by the type_id column
 * @method     ItemQuery orderByName($order = Criteria::ASC) Order by the name column
 *
 * @method     ItemQuery groupByDataId() Group by the data_id column
 * @method     ItemQuery groupByTypeId() Group by the type_id column
 * @method     ItemQuery groupByName() Group by the name column
 *
 * @method     ItemQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ItemQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ItemQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ItemQuery leftJoinItemType($relationAlias = null) Adds a LEFT JOIN clause to the query using the ItemType relation
 * @method     ItemQuery rightJoinItemType($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ItemType relation
 * @method     ItemQuery innerJoinItemType($relationAlias = null) Adds a INNER JOIN clause to the query using the ItemType relation
 *
 * @method     Item findOne(PropelPDO $con = null) Return the first Item matching the query
 * @method     Item findOneOrCreate(PropelPDO $con = null) Return the first Item matching the query, or a new Item object populated from the query conditions when no match is found
 *
 * @method     Item findOneByDataId(int $data_id) Return the first Item filtered by the data_id column
 * @method     Item findOneByTypeId(int $type_id) Return the first Item filtered by the type_id column
 * @method     Item findOneByName(string $name) Return the first Item filtered by the name column
 *
 * @method     array findByDataId(int $data_id) Return Item objects filtered by the data_id column
 * @method     array findByTypeId(int $type_id) Return Item objects filtered by the type_id column
 * @method     array findByName(string $name) Return Item objects filtered by the name column
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
        $sql = 'SELECT `DATA_ID`, `TYPE_ID`, `NAME` FROM `item` WHERE `DATA_ID` = :p0';
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
     * @see       filterByItemType()
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
                ->addUsingAlias(ItemPeer::TYPE_ID, $itemType->getId(), $comparison);
        } elseif ($itemType instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ItemPeer::TYPE_ID, $itemType->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return   \\GW2Spidy\DB\ItemTypeQuery A secondary query class using the current class as primary query
     */
    public function useItemTypeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinItemType($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ItemType', '\\GW2Spidy\DB\ItemTypeQuery');
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