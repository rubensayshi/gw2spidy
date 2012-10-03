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
use GW2Spidy\DB\Discipline;
use GW2Spidy\DB\DisciplinePeer;
use GW2Spidy\DB\DisciplineQuery;
use GW2Spidy\DB\Recipe;

/**
 * Base class that represents a query for the 'discipline' table.
 *
 * 
 *
 * @method     DisciplineQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     DisciplineQuery orderByName($order = Criteria::ASC) Order by the name column
 *
 * @method     DisciplineQuery groupById() Group by the id column
 * @method     DisciplineQuery groupByName() Group by the name column
 *
 * @method     DisciplineQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     DisciplineQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     DisciplineQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     DisciplineQuery leftJoinRecipe($relationAlias = null) Adds a LEFT JOIN clause to the query using the Recipe relation
 * @method     DisciplineQuery rightJoinRecipe($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Recipe relation
 * @method     DisciplineQuery innerJoinRecipe($relationAlias = null) Adds a INNER JOIN clause to the query using the Recipe relation
 *
 * @method     Discipline findOne(PropelPDO $con = null) Return the first Discipline matching the query
 * @method     Discipline findOneOrCreate(PropelPDO $con = null) Return the first Discipline matching the query, or a new Discipline object populated from the query conditions when no match is found
 *
 * @method     Discipline findOneById(int $id) Return the first Discipline filtered by the id column
 * @method     Discipline findOneByName(string $name) Return the first Discipline filtered by the name column
 *
 * @method     array findById(int $id) Return Discipline objects filtered by the id column
 * @method     array findByName(string $name) Return Discipline objects filtered by the name column
 *
 * @package    propel.generator.gw2spidy.om
 */
abstract class BaseDisciplineQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseDisciplineQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'gw2spidy', $modelName = 'GW2Spidy\\DB\\Discipline', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new DisciplineQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     DisciplineQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return DisciplineQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof DisciplineQuery) {
            return $criteria;
        }
        $query = new DisciplineQuery();
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
     * @return   Discipline|Discipline[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DisciplinePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(DisciplinePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   Discipline A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `ID`, `NAME` FROM `discipline` WHERE `ID` = :p0';
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
            $obj = new Discipline();
            $obj->hydrate($row);
            DisciplinePeer::addInstanceToPool($obj, (string) $key);
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
     * @return Discipline|Discipline[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Discipline[]|mixed the list of results, formatted by the current formatter
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
     * @return DisciplineQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(DisciplinePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return DisciplineQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(DisciplinePeer::ID, $keys, Criteria::IN);
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
     * @return DisciplineQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(DisciplinePeer::ID, $id, $comparison);
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
     * @return DisciplineQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DisciplinePeer::NAME, $name, $comparison);
    }

    /**
     * Filter the query by a related Recipe object
     *
     * @param   Recipe|PropelObjectCollection $recipe  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   DisciplineQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByRecipe($recipe, $comparison = null)
    {
        if ($recipe instanceof Recipe) {
            return $this
                ->addUsingAlias(DisciplinePeer::ID, $recipe->getDisciplineId(), $comparison);
        } elseif ($recipe instanceof PropelObjectCollection) {
            return $this
                ->useRecipeQuery()
                ->filterByPrimaryKeys($recipe->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByRecipe() only accepts arguments of type Recipe or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Recipe relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return DisciplineQuery The current query, for fluid interface
     */
    public function joinRecipe($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Recipe');

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
            $this->addJoinObject($join, 'Recipe');
        }

        return $this;
    }

    /**
     * Use the Recipe relation Recipe object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \GW2Spidy\DB\RecipeQuery A secondary query class using the current class as primary query
     */
    public function useRecipeQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinRecipe($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Recipe', '\GW2Spidy\DB\RecipeQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Discipline $discipline Object to remove from the list of results
     *
     * @return DisciplineQuery The current query, for fluid interface
     */
    public function prune($discipline = null)
    {
        if ($discipline) {
            $this->addUsingAlias(DisciplinePeer::ID, $discipline->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseDisciplineQuery