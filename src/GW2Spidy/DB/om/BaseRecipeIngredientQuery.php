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
use GW2Spidy\DB\Recipe;
use GW2Spidy\DB\RecipeIngredient;
use GW2Spidy\DB\RecipeIngredientPeer;
use GW2Spidy\DB\RecipeIngredientQuery;

/**
 * Base class that represents a query for the 'recipe_ingredient' table.
 *
 * 
 *
 * @method     RecipeIngredientQuery orderByRecipeId($order = Criteria::ASC) Order by the recipe_id column
 * @method     RecipeIngredientQuery orderByItemId($order = Criteria::ASC) Order by the item_id column
 * @method     RecipeIngredientQuery orderByCount($order = Criteria::ASC) Order by the count column
 *
 * @method     RecipeIngredientQuery groupByRecipeId() Group by the recipe_id column
 * @method     RecipeIngredientQuery groupByItemId() Group by the item_id column
 * @method     RecipeIngredientQuery groupByCount() Group by the count column
 *
 * @method     RecipeIngredientQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     RecipeIngredientQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     RecipeIngredientQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     RecipeIngredientQuery leftJoinRecipe($relationAlias = null) Adds a LEFT JOIN clause to the query using the Recipe relation
 * @method     RecipeIngredientQuery rightJoinRecipe($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Recipe relation
 * @method     RecipeIngredientQuery innerJoinRecipe($relationAlias = null) Adds a INNER JOIN clause to the query using the Recipe relation
 *
 * @method     RecipeIngredientQuery leftJoinItem($relationAlias = null) Adds a LEFT JOIN clause to the query using the Item relation
 * @method     RecipeIngredientQuery rightJoinItem($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Item relation
 * @method     RecipeIngredientQuery innerJoinItem($relationAlias = null) Adds a INNER JOIN clause to the query using the Item relation
 *
 * @method     RecipeIngredient findOne(PropelPDO $con = null) Return the first RecipeIngredient matching the query
 * @method     RecipeIngredient findOneOrCreate(PropelPDO $con = null) Return the first RecipeIngredient matching the query, or a new RecipeIngredient object populated from the query conditions when no match is found
 *
 * @method     RecipeIngredient findOneByRecipeId(int $recipe_id) Return the first RecipeIngredient filtered by the recipe_id column
 * @method     RecipeIngredient findOneByItemId(int $item_id) Return the first RecipeIngredient filtered by the item_id column
 * @method     RecipeIngredient findOneByCount(int $count) Return the first RecipeIngredient filtered by the count column
 *
 * @method     array findByRecipeId(int $recipe_id) Return RecipeIngredient objects filtered by the recipe_id column
 * @method     array findByItemId(int $item_id) Return RecipeIngredient objects filtered by the item_id column
 * @method     array findByCount(int $count) Return RecipeIngredient objects filtered by the count column
 *
 * @package    propel.generator.gw2spidy.om
 */
abstract class BaseRecipeIngredientQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseRecipeIngredientQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'gw2spidy', $modelName = 'GW2Spidy\\DB\\RecipeIngredient', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new RecipeIngredientQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     RecipeIngredientQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return RecipeIngredientQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof RecipeIngredientQuery) {
            return $criteria;
        }
        $query = new RecipeIngredientQuery();
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
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array $key Primary key to use for the query 
                         A Primary key composition: [$recipe_id, $item_id]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   RecipeIngredient|RecipeIngredient[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = RecipeIngredientPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(RecipeIngredientPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   RecipeIngredient A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `RECIPE_ID`, `ITEM_ID`, `COUNT` FROM `recipe_ingredient` WHERE `RECIPE_ID` = :p0 AND `ITEM_ID` = :p1';
        try {
            $stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
			$stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new RecipeIngredient();
            $obj->hydrate($row);
            RecipeIngredientPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return RecipeIngredient|RecipeIngredient[]|mixed the result, formatted by the current formatter
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
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|RecipeIngredient[]|mixed the list of results, formatted by the current formatter
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
     * @return RecipeIngredientQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(RecipeIngredientPeer::RECIPE_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(RecipeIngredientPeer::ITEM_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return RecipeIngredientQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(RecipeIngredientPeer::RECIPE_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(RecipeIngredientPeer::ITEM_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the recipe_id column
     *
     * Example usage:
     * <code>
     * $query->filterByRecipeId(1234); // WHERE recipe_id = 1234
     * $query->filterByRecipeId(array(12, 34)); // WHERE recipe_id IN (12, 34)
     * $query->filterByRecipeId(array('min' => 12)); // WHERE recipe_id > 12
     * </code>
     *
     * @see       filterByRecipe()
     *
     * @param     mixed $recipeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RecipeIngredientQuery The current query, for fluid interface
     */
    public function filterByRecipeId($recipeId = null, $comparison = null)
    {
        if (is_array($recipeId) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(RecipeIngredientPeer::RECIPE_ID, $recipeId, $comparison);
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
     * @return RecipeIngredientQuery The current query, for fluid interface
     */
    public function filterByItemId($itemId = null, $comparison = null)
    {
        if (is_array($itemId) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(RecipeIngredientPeer::ITEM_ID, $itemId, $comparison);
    }

    /**
     * Filter the query on the count column
     *
     * Example usage:
     * <code>
     * $query->filterByCount(1234); // WHERE count = 1234
     * $query->filterByCount(array(12, 34)); // WHERE count IN (12, 34)
     * $query->filterByCount(array('min' => 12)); // WHERE count > 12
     * </code>
     *
     * @param     mixed $count The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RecipeIngredientQuery The current query, for fluid interface
     */
    public function filterByCount($count = null, $comparison = null)
    {
        if (is_array($count)) {
            $useMinMax = false;
            if (isset($count['min'])) {
                $this->addUsingAlias(RecipeIngredientPeer::COUNT, $count['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($count['max'])) {
                $this->addUsingAlias(RecipeIngredientPeer::COUNT, $count['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RecipeIngredientPeer::COUNT, $count, $comparison);
    }

    /**
     * Filter the query by a related Recipe object
     *
     * @param   Recipe|PropelObjectCollection $recipe The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   RecipeIngredientQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByRecipe($recipe, $comparison = null)
    {
        if ($recipe instanceof Recipe) {
            return $this
                ->addUsingAlias(RecipeIngredientPeer::RECIPE_ID, $recipe->getDataId(), $comparison);
        } elseif ($recipe instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(RecipeIngredientPeer::RECIPE_ID, $recipe->toKeyValue('PrimaryKey', 'DataId'), $comparison);
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
     * @return RecipeIngredientQuery The current query, for fluid interface
     */
    public function joinRecipe($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
    public function useRecipeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinRecipe($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Recipe', '\GW2Spidy\DB\RecipeQuery');
    }

    /**
     * Filter the query by a related Item object
     *
     * @param   Item|PropelObjectCollection $item The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   RecipeIngredientQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByItem($item, $comparison = null)
    {
        if ($item instanceof Item) {
            return $this
                ->addUsingAlias(RecipeIngredientPeer::ITEM_ID, $item->getDataId(), $comparison);
        } elseif ($item instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(RecipeIngredientPeer::ITEM_ID, $item->toKeyValue('PrimaryKey', 'DataId'), $comparison);
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
     * @return RecipeIngredientQuery The current query, for fluid interface
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
     * @param   RecipeIngredient $recipeIngredient Object to remove from the list of results
     *
     * @return RecipeIngredientQuery The current query, for fluid interface
     */
    public function prune($recipeIngredient = null)
    {
        if ($recipeIngredient) {
            $this->addCond('pruneCond0', $this->getAliasedColName(RecipeIngredientPeer::RECIPE_ID), $recipeIngredient->getRecipeId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(RecipeIngredientPeer::ITEM_ID), $recipeIngredient->getItemId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

} // BaseRecipeIngredientQuery