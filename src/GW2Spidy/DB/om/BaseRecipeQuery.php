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
use GW2Spidy\DB\Item;
use GW2Spidy\DB\Recipe;
use GW2Spidy\DB\RecipeIngredient;
use GW2Spidy\DB\RecipePeer;
use GW2Spidy\DB\RecipeQuery;

/**
 * Base class that represents a query for the 'recipe' table.
 *
 * 
 *
 * @method     RecipeQuery orderByDataId($order = Criteria::ASC) Order by the data_id column
 * @method     RecipeQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     RecipeQuery orderByDisciplineId($order = Criteria::ASC) Order by the discipline_id column
 * @method     RecipeQuery orderByRating($order = Criteria::ASC) Order by the rating column
 * @method     RecipeQuery orderByResultItemId($order = Criteria::ASC) Order by the result_item_id column
 * @method     RecipeQuery orderByCount($order = Criteria::ASC) Order by the count column
 * @method     RecipeQuery orderByCost($order = Criteria::ASC) Order by the cost column
 * @method     RecipeQuery orderBySellPrice($order = Criteria::ASC) Order by the sell_price column
 * @method     RecipeQuery orderByProfit($order = Criteria::ASC) Order by the profit column
 * @method     RecipeQuery orderByUpdated($order = Criteria::ASC) Order by the updated column
 * @method     RecipeQuery orderByRequiresUnlock($order = Criteria::ASC) Order by the requires_unlock column
 * @method     RecipeQuery orderByGw2dbId($order = Criteria::ASC) Order by the gw2db_id column
 * @method     RecipeQuery orderByGw2dbExternalId($order = Criteria::ASC) Order by the gw2db_external_id column
 *
 * @method     RecipeQuery groupByDataId() Group by the data_id column
 * @method     RecipeQuery groupByName() Group by the name column
 * @method     RecipeQuery groupByDisciplineId() Group by the discipline_id column
 * @method     RecipeQuery groupByRating() Group by the rating column
 * @method     RecipeQuery groupByResultItemId() Group by the result_item_id column
 * @method     RecipeQuery groupByCount() Group by the count column
 * @method     RecipeQuery groupByCost() Group by the cost column
 * @method     RecipeQuery groupBySellPrice() Group by the sell_price column
 * @method     RecipeQuery groupByProfit() Group by the profit column
 * @method     RecipeQuery groupByUpdated() Group by the updated column
 * @method     RecipeQuery groupByRequiresUnlock() Group by the requires_unlock column
 * @method     RecipeQuery groupByGw2dbId() Group by the gw2db_id column
 * @method     RecipeQuery groupByGw2dbExternalId() Group by the gw2db_external_id column
 *
 * @method     RecipeQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     RecipeQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     RecipeQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     RecipeQuery leftJoinDiscipline($relationAlias = null) Adds a LEFT JOIN clause to the query using the Discipline relation
 * @method     RecipeQuery rightJoinDiscipline($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Discipline relation
 * @method     RecipeQuery innerJoinDiscipline($relationAlias = null) Adds a INNER JOIN clause to the query using the Discipline relation
 *
 * @method     RecipeQuery leftJoinResultItem($relationAlias = null) Adds a LEFT JOIN clause to the query using the ResultItem relation
 * @method     RecipeQuery rightJoinResultItem($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ResultItem relation
 * @method     RecipeQuery innerJoinResultItem($relationAlias = null) Adds a INNER JOIN clause to the query using the ResultItem relation
 *
 * @method     RecipeQuery leftJoinIngredient($relationAlias = null) Adds a LEFT JOIN clause to the query using the Ingredient relation
 * @method     RecipeQuery rightJoinIngredient($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Ingredient relation
 * @method     RecipeQuery innerJoinIngredient($relationAlias = null) Adds a INNER JOIN clause to the query using the Ingredient relation
 *
 * @method     Recipe findOne(PropelPDO $con = null) Return the first Recipe matching the query
 * @method     Recipe findOneOrCreate(PropelPDO $con = null) Return the first Recipe matching the query, or a new Recipe object populated from the query conditions when no match is found
 *
 * @method     Recipe findOneByDataId(int $data_id) Return the first Recipe filtered by the data_id column
 * @method     Recipe findOneByName(string $name) Return the first Recipe filtered by the name column
 * @method     Recipe findOneByDisciplineId(int $discipline_id) Return the first Recipe filtered by the discipline_id column
 * @method     Recipe findOneByRating(int $rating) Return the first Recipe filtered by the rating column
 * @method     Recipe findOneByResultItemId(int $result_item_id) Return the first Recipe filtered by the result_item_id column
 * @method     Recipe findOneByCount(int $count) Return the first Recipe filtered by the count column
 * @method     Recipe findOneByCost(int $cost) Return the first Recipe filtered by the cost column
 * @method     Recipe findOneBySellPrice(int $sell_price) Return the first Recipe filtered by the sell_price column
 * @method     Recipe findOneByProfit(int $profit) Return the first Recipe filtered by the profit column
 * @method     Recipe findOneByUpdated(string $updated) Return the first Recipe filtered by the updated column
 * @method     Recipe findOneByRequiresUnlock(int $requires_unlock) Return the first Recipe filtered by the requires_unlock column
 * @method     Recipe findOneByGw2dbId(int $gw2db_id) Return the first Recipe filtered by the gw2db_id column
 * @method     Recipe findOneByGw2dbExternalId(int $gw2db_external_id) Return the first Recipe filtered by the gw2db_external_id column
 *
 * @method     array findByDataId(int $data_id) Return Recipe objects filtered by the data_id column
 * @method     array findByName(string $name) Return Recipe objects filtered by the name column
 * @method     array findByDisciplineId(int $discipline_id) Return Recipe objects filtered by the discipline_id column
 * @method     array findByRating(int $rating) Return Recipe objects filtered by the rating column
 * @method     array findByResultItemId(int $result_item_id) Return Recipe objects filtered by the result_item_id column
 * @method     array findByCount(int $count) Return Recipe objects filtered by the count column
 * @method     array findByCost(int $cost) Return Recipe objects filtered by the cost column
 * @method     array findBySellPrice(int $sell_price) Return Recipe objects filtered by the sell_price column
 * @method     array findByProfit(int $profit) Return Recipe objects filtered by the profit column
 * @method     array findByUpdated(string $updated) Return Recipe objects filtered by the updated column
 * @method     array findByRequiresUnlock(int $requires_unlock) Return Recipe objects filtered by the requires_unlock column
 * @method     array findByGw2dbId(int $gw2db_id) Return Recipe objects filtered by the gw2db_id column
 * @method     array findByGw2dbExternalId(int $gw2db_external_id) Return Recipe objects filtered by the gw2db_external_id column
 *
 * @package    propel.generator.gw2spidy.om
 */
abstract class BaseRecipeQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseRecipeQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'gw2spidy', $modelName = 'GW2Spidy\\DB\\Recipe', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new RecipeQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     RecipeQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return RecipeQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof RecipeQuery) {
            return $criteria;
        }
        $query = new RecipeQuery();
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
     * @return   Recipe|Recipe[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = RecipePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(RecipePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   Recipe A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `DATA_ID`, `NAME`, `DISCIPLINE_ID`, `RATING`, `RESULT_ITEM_ID`, `COUNT`, `COST`, `SELL_PRICE`, `PROFIT`, `UPDATED`, `REQUIRES_UNLOCK`, `GW2DB_ID`, `GW2DB_EXTERNAL_ID` FROM `recipe` WHERE `DATA_ID` = :p0';
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
            $obj = new Recipe();
            $obj->hydrate($row);
            RecipePeer::addInstanceToPool($obj, (string) $key);
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
     * @return Recipe|Recipe[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Recipe[]|mixed the list of results, formatted by the current formatter
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
     * @return RecipeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(RecipePeer::DATA_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return RecipeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(RecipePeer::DATA_ID, $keys, Criteria::IN);
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
     * @return RecipeQuery The current query, for fluid interface
     */
    public function filterByDataId($dataId = null, $comparison = null)
    {
        if (is_array($dataId) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(RecipePeer::DATA_ID, $dataId, $comparison);
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
     * @return RecipeQuery The current query, for fluid interface
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

        return $this->addUsingAlias(RecipePeer::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the discipline_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDisciplineId(1234); // WHERE discipline_id = 1234
     * $query->filterByDisciplineId(array(12, 34)); // WHERE discipline_id IN (12, 34)
     * $query->filterByDisciplineId(array('min' => 12)); // WHERE discipline_id > 12
     * </code>
     *
     * @see       filterByDiscipline()
     *
     * @param     mixed $disciplineId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RecipeQuery The current query, for fluid interface
     */
    public function filterByDisciplineId($disciplineId = null, $comparison = null)
    {
        if (is_array($disciplineId)) {
            $useMinMax = false;
            if (isset($disciplineId['min'])) {
                $this->addUsingAlias(RecipePeer::DISCIPLINE_ID, $disciplineId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($disciplineId['max'])) {
                $this->addUsingAlias(RecipePeer::DISCIPLINE_ID, $disciplineId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RecipePeer::DISCIPLINE_ID, $disciplineId, $comparison);
    }

    /**
     * Filter the query on the rating column
     *
     * Example usage:
     * <code>
     * $query->filterByRating(1234); // WHERE rating = 1234
     * $query->filterByRating(array(12, 34)); // WHERE rating IN (12, 34)
     * $query->filterByRating(array('min' => 12)); // WHERE rating > 12
     * </code>
     *
     * @param     mixed $rating The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RecipeQuery The current query, for fluid interface
     */
    public function filterByRating($rating = null, $comparison = null)
    {
        if (is_array($rating)) {
            $useMinMax = false;
            if (isset($rating['min'])) {
                $this->addUsingAlias(RecipePeer::RATING, $rating['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($rating['max'])) {
                $this->addUsingAlias(RecipePeer::RATING, $rating['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RecipePeer::RATING, $rating, $comparison);
    }

    /**
     * Filter the query on the result_item_id column
     *
     * Example usage:
     * <code>
     * $query->filterByResultItemId(1234); // WHERE result_item_id = 1234
     * $query->filterByResultItemId(array(12, 34)); // WHERE result_item_id IN (12, 34)
     * $query->filterByResultItemId(array('min' => 12)); // WHERE result_item_id > 12
     * </code>
     *
     * @see       filterByResultItem()
     *
     * @param     mixed $resultItemId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RecipeQuery The current query, for fluid interface
     */
    public function filterByResultItemId($resultItemId = null, $comparison = null)
    {
        if (is_array($resultItemId)) {
            $useMinMax = false;
            if (isset($resultItemId['min'])) {
                $this->addUsingAlias(RecipePeer::RESULT_ITEM_ID, $resultItemId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($resultItemId['max'])) {
                $this->addUsingAlias(RecipePeer::RESULT_ITEM_ID, $resultItemId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RecipePeer::RESULT_ITEM_ID, $resultItemId, $comparison);
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
     * @return RecipeQuery The current query, for fluid interface
     */
    public function filterByCount($count = null, $comparison = null)
    {
        if (is_array($count)) {
            $useMinMax = false;
            if (isset($count['min'])) {
                $this->addUsingAlias(RecipePeer::COUNT, $count['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($count['max'])) {
                $this->addUsingAlias(RecipePeer::COUNT, $count['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RecipePeer::COUNT, $count, $comparison);
    }

    /**
     * Filter the query on the cost column
     *
     * Example usage:
     * <code>
     * $query->filterByCost(1234); // WHERE cost = 1234
     * $query->filterByCost(array(12, 34)); // WHERE cost IN (12, 34)
     * $query->filterByCost(array('min' => 12)); // WHERE cost > 12
     * </code>
     *
     * @param     mixed $cost The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RecipeQuery The current query, for fluid interface
     */
    public function filterByCost($cost = null, $comparison = null)
    {
        if (is_array($cost)) {
            $useMinMax = false;
            if (isset($cost['min'])) {
                $this->addUsingAlias(RecipePeer::COST, $cost['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($cost['max'])) {
                $this->addUsingAlias(RecipePeer::COST, $cost['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RecipePeer::COST, $cost, $comparison);
    }

    /**
     * Filter the query on the sell_price column
     *
     * Example usage:
     * <code>
     * $query->filterBySellPrice(1234); // WHERE sell_price = 1234
     * $query->filterBySellPrice(array(12, 34)); // WHERE sell_price IN (12, 34)
     * $query->filterBySellPrice(array('min' => 12)); // WHERE sell_price > 12
     * </code>
     *
     * @param     mixed $sellPrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RecipeQuery The current query, for fluid interface
     */
    public function filterBySellPrice($sellPrice = null, $comparison = null)
    {
        if (is_array($sellPrice)) {
            $useMinMax = false;
            if (isset($sellPrice['min'])) {
                $this->addUsingAlias(RecipePeer::SELL_PRICE, $sellPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sellPrice['max'])) {
                $this->addUsingAlias(RecipePeer::SELL_PRICE, $sellPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RecipePeer::SELL_PRICE, $sellPrice, $comparison);
    }

    /**
     * Filter the query on the profit column
     *
     * Example usage:
     * <code>
     * $query->filterByProfit(1234); // WHERE profit = 1234
     * $query->filterByProfit(array(12, 34)); // WHERE profit IN (12, 34)
     * $query->filterByProfit(array('min' => 12)); // WHERE profit > 12
     * </code>
     *
     * @param     mixed $profit The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RecipeQuery The current query, for fluid interface
     */
    public function filterByProfit($profit = null, $comparison = null)
    {
        if (is_array($profit)) {
            $useMinMax = false;
            if (isset($profit['min'])) {
                $this->addUsingAlias(RecipePeer::PROFIT, $profit['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($profit['max'])) {
                $this->addUsingAlias(RecipePeer::PROFIT, $profit['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RecipePeer::PROFIT, $profit, $comparison);
    }

    /**
     * Filter the query on the updated column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdated('2011-03-14'); // WHERE updated = '2011-03-14'
     * $query->filterByUpdated('now'); // WHERE updated = '2011-03-14'
     * $query->filterByUpdated(array('max' => 'yesterday')); // WHERE updated > '2011-03-13'
     * </code>
     *
     * @param     mixed $updated The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RecipeQuery The current query, for fluid interface
     */
    public function filterByUpdated($updated = null, $comparison = null)
    {
        if (is_array($updated)) {
            $useMinMax = false;
            if (isset($updated['min'])) {
                $this->addUsingAlias(RecipePeer::UPDATED, $updated['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updated['max'])) {
                $this->addUsingAlias(RecipePeer::UPDATED, $updated['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RecipePeer::UPDATED, $updated, $comparison);
    }

    /**
     * Filter the query on the requires_unlock column
     *
     * Example usage:
     * <code>
     * $query->filterByRequiresUnlock(1234); // WHERE requires_unlock = 1234
     * $query->filterByRequiresUnlock(array(12, 34)); // WHERE requires_unlock IN (12, 34)
     * $query->filterByRequiresUnlock(array('min' => 12)); // WHERE requires_unlock > 12
     * </code>
     *
     * @param     mixed $requiresUnlock The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RecipeQuery The current query, for fluid interface
     */
    public function filterByRequiresUnlock($requiresUnlock = null, $comparison = null)
    {
        if (is_array($requiresUnlock)) {
            $useMinMax = false;
            if (isset($requiresUnlock['min'])) {
                $this->addUsingAlias(RecipePeer::REQUIRES_UNLOCK, $requiresUnlock['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($requiresUnlock['max'])) {
                $this->addUsingAlias(RecipePeer::REQUIRES_UNLOCK, $requiresUnlock['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RecipePeer::REQUIRES_UNLOCK, $requiresUnlock, $comparison);
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
     * @return RecipeQuery The current query, for fluid interface
     */
    public function filterByGw2dbId($gw2dbId = null, $comparison = null)
    {
        if (is_array($gw2dbId)) {
            $useMinMax = false;
            if (isset($gw2dbId['min'])) {
                $this->addUsingAlias(RecipePeer::GW2DB_ID, $gw2dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($gw2dbId['max'])) {
                $this->addUsingAlias(RecipePeer::GW2DB_ID, $gw2dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RecipePeer::GW2DB_ID, $gw2dbId, $comparison);
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
     * @return RecipeQuery The current query, for fluid interface
     */
    public function filterByGw2dbExternalId($gw2dbExternalId = null, $comparison = null)
    {
        if (is_array($gw2dbExternalId)) {
            $useMinMax = false;
            if (isset($gw2dbExternalId['min'])) {
                $this->addUsingAlias(RecipePeer::GW2DB_EXTERNAL_ID, $gw2dbExternalId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($gw2dbExternalId['max'])) {
                $this->addUsingAlias(RecipePeer::GW2DB_EXTERNAL_ID, $gw2dbExternalId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RecipePeer::GW2DB_EXTERNAL_ID, $gw2dbExternalId, $comparison);
    }

    /**
     * Filter the query by a related Discipline object
     *
     * @param   Discipline|PropelObjectCollection $discipline The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   RecipeQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByDiscipline($discipline, $comparison = null)
    {
        if ($discipline instanceof Discipline) {
            return $this
                ->addUsingAlias(RecipePeer::DISCIPLINE_ID, $discipline->getId(), $comparison);
        } elseif ($discipline instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(RecipePeer::DISCIPLINE_ID, $discipline->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByDiscipline() only accepts arguments of type Discipline or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Discipline relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return RecipeQuery The current query, for fluid interface
     */
    public function joinDiscipline($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Discipline');

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
            $this->addJoinObject($join, 'Discipline');
        }

        return $this;
    }

    /**
     * Use the Discipline relation Discipline object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \GW2Spidy\DB\DisciplineQuery A secondary query class using the current class as primary query
     */
    public function useDisciplineQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinDiscipline($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Discipline', '\GW2Spidy\DB\DisciplineQuery');
    }

    /**
     * Filter the query by a related Item object
     *
     * @param   Item|PropelObjectCollection $item The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   RecipeQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByResultItem($item, $comparison = null)
    {
        if ($item instanceof Item) {
            return $this
                ->addUsingAlias(RecipePeer::RESULT_ITEM_ID, $item->getDataId(), $comparison);
        } elseif ($item instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(RecipePeer::RESULT_ITEM_ID, $item->toKeyValue('PrimaryKey', 'DataId'), $comparison);
        } else {
            throw new PropelException('filterByResultItem() only accepts arguments of type Item or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ResultItem relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return RecipeQuery The current query, for fluid interface
     */
    public function joinResultItem($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ResultItem');

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
            $this->addJoinObject($join, 'ResultItem');
        }

        return $this;
    }

    /**
     * Use the ResultItem relation Item object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \GW2Spidy\DB\ItemQuery A secondary query class using the current class as primary query
     */
    public function useResultItemQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinResultItem($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ResultItem', '\GW2Spidy\DB\ItemQuery');
    }

    /**
     * Filter the query by a related RecipeIngredient object
     *
     * @param   RecipeIngredient|PropelObjectCollection $recipeIngredient  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   RecipeQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByIngredient($recipeIngredient, $comparison = null)
    {
        if ($recipeIngredient instanceof RecipeIngredient) {
            return $this
                ->addUsingAlias(RecipePeer::DATA_ID, $recipeIngredient->getRecipeId(), $comparison);
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
     * @return RecipeQuery The current query, for fluid interface
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
     * Filter the query by a related Item object
     * using the recipe_ingredient table as cross reference
     *
     * @param   Item $item the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   RecipeQuery The current query, for fluid interface
     */
    public function filterByItem($item, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useIngredientQuery()
            ->filterByItem($item, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   Recipe $recipe Object to remove from the list of results
     *
     * @return RecipeQuery The current query, for fluid interface
     */
    public function prune($recipe = null)
    {
        if ($recipe) {
            $this->addUsingAlias(RecipePeer::DATA_ID, $recipe->getDataId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseRecipeQuery