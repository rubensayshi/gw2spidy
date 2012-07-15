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
use GW2Spidy\DB\ItemSubType;
use GW2Spidy\DB\ItemSubTypePeer;
use GW2Spidy\DB\ItemSubTypeQuery;
use GW2Spidy\DB\ItemType;

/**
 * Base class that represents a query for the 'item_sub_type' table.
 *
 * 
 *
 * @method     ItemSubTypeQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ItemSubTypeQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method     ItemSubTypeQuery orderByMainTypeId($order = Criteria::ASC) Order by the main_type_id column
 *
 * @method     ItemSubTypeQuery groupById() Group by the id column
 * @method     ItemSubTypeQuery groupByTitle() Group by the title column
 * @method     ItemSubTypeQuery groupByMainTypeId() Group by the main_type_id column
 *
 * @method     ItemSubTypeQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ItemSubTypeQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ItemSubTypeQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ItemSubTypeQuery leftJoinMainType($relationAlias = null) Adds a LEFT JOIN clause to the query using the MainType relation
 * @method     ItemSubTypeQuery rightJoinMainType($relationAlias = null) Adds a RIGHT JOIN clause to the query using the MainType relation
 * @method     ItemSubTypeQuery innerJoinMainType($relationAlias = null) Adds a INNER JOIN clause to the query using the MainType relation
 *
 * @method     ItemSubType findOne(PropelPDO $con = null) Return the first ItemSubType matching the query
 * @method     ItemSubType findOneOrCreate(PropelPDO $con = null) Return the first ItemSubType matching the query, or a new ItemSubType object populated from the query conditions when no match is found
 *
 * @method     ItemSubType findOneById(int $id) Return the first ItemSubType filtered by the id column
 * @method     ItemSubType findOneByTitle(string $title) Return the first ItemSubType filtered by the title column
 * @method     ItemSubType findOneByMainTypeId(int $main_type_id) Return the first ItemSubType filtered by the main_type_id column
 *
 * @method     array findById(int $id) Return ItemSubType objects filtered by the id column
 * @method     array findByTitle(string $title) Return ItemSubType objects filtered by the title column
 * @method     array findByMainTypeId(int $main_type_id) Return ItemSubType objects filtered by the main_type_id column
 *
 * @package    propel.generator.gw2spidy.om
 */
abstract class BaseItemSubTypeQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseItemSubTypeQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'gw2spidy', $modelName = 'GW2Spidy\\DB\\ItemSubType', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ItemSubTypeQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     ItemSubTypeQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ItemSubTypeQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ItemSubTypeQuery) {
            return $criteria;
        }
        $query = new ItemSubTypeQuery();
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
     * @return   ItemSubType|ItemSubType[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ItemSubTypePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ItemSubTypePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   ItemSubType A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `ID`, `TITLE`, `MAIN_TYPE_ID` FROM `item_sub_type` WHERE `ID` = :p0';
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
            $obj = new ItemSubType();
            $obj->hydrate($row);
            ItemSubTypePeer::addInstanceToPool($obj, (string) $key);
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
     * @return ItemSubType|ItemSubType[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|ItemSubType[]|mixed the list of results, formatted by the current formatter
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
     * @return ItemSubTypeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ItemSubTypePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ItemSubTypeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ItemSubTypePeer::ID, $keys, Criteria::IN);
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
     * @return ItemSubTypeQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(ItemSubTypePeer::ID, $id, $comparison);
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
     * @return ItemSubTypeQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ItemSubTypePeer::TITLE, $title, $comparison);
    }

    /**
     * Filter the query on the main_type_id column
     *
     * Example usage:
     * <code>
     * $query->filterByMainTypeId(1234); // WHERE main_type_id = 1234
     * $query->filterByMainTypeId(array(12, 34)); // WHERE main_type_id IN (12, 34)
     * $query->filterByMainTypeId(array('min' => 12)); // WHERE main_type_id > 12
     * </code>
     *
     * @see       filterByMainType()
     *
     * @param     mixed $mainTypeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ItemSubTypeQuery The current query, for fluid interface
     */
    public function filterByMainTypeId($mainTypeId = null, $comparison = null)
    {
        if (is_array($mainTypeId)) {
            $useMinMax = false;
            if (isset($mainTypeId['min'])) {
                $this->addUsingAlias(ItemSubTypePeer::MAIN_TYPE_ID, $mainTypeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($mainTypeId['max'])) {
                $this->addUsingAlias(ItemSubTypePeer::MAIN_TYPE_ID, $mainTypeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ItemSubTypePeer::MAIN_TYPE_ID, $mainTypeId, $comparison);
    }

    /**
     * Filter the query by a related ItemType object
     *
     * @param   ItemType|PropelObjectCollection $itemType The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   ItemSubTypeQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByMainType($itemType, $comparison = null)
    {
        if ($itemType instanceof ItemType) {
            return $this
                ->addUsingAlias(ItemSubTypePeer::MAIN_TYPE_ID, $itemType->getId(), $comparison);
        } elseif ($itemType instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ItemSubTypePeer::MAIN_TYPE_ID, $itemType->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByMainType() only accepts arguments of type ItemType or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the MainType relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ItemSubTypeQuery The current query, for fluid interface
     */
    public function joinMainType($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('MainType');

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
            $this->addJoinObject($join, 'MainType');
        }

        return $this;
    }

    /**
     * Use the MainType relation ItemType object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \GW2Spidy\DB\ItemTypeQuery A secondary query class using the current class as primary query
     */
    public function useMainTypeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinMainType($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'MainType', '\GW2Spidy\DB\ItemTypeQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ItemSubType $itemSubType Object to remove from the list of results
     *
     * @return ItemSubTypeQuery The current query, for fluid interface
     */
    public function prune($itemSubType = null)
    {
        if ($itemSubType) {
            $this->addUsingAlias(ItemSubTypePeer::ID, $itemSubType->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseItemSubTypeQuery