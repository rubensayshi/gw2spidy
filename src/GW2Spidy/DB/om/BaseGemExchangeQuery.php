<?php

namespace GW2Spidy\DB\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use GW2Spidy\DB\GemExchange;
use GW2Spidy\DB\GemExchangePeer;
use GW2Spidy\DB\GemExchangeQuery;

/**
 * Base class that represents a query for the 'gem_exchange' table.
 *
 * 
 *
 * @method     GemExchangeQuery orderByExchangeDate($order = Criteria::ASC) Order by the exchange_date column
 * @method     GemExchangeQuery orderByExchangeTime($order = Criteria::ASC) Order by the exchange_time column
 * @method     GemExchangeQuery orderByAverage($order = Criteria::ASC) Order by the average column
 *
 * @method     GemExchangeQuery groupByExchangeDate() Group by the exchange_date column
 * @method     GemExchangeQuery groupByExchangeTime() Group by the exchange_time column
 * @method     GemExchangeQuery groupByAverage() Group by the average column
 *
 * @method     GemExchangeQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     GemExchangeQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     GemExchangeQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     GemExchange findOne(PropelPDO $con = null) Return the first GemExchange matching the query
 * @method     GemExchange findOneOrCreate(PropelPDO $con = null) Return the first GemExchange matching the query, or a new GemExchange object populated from the query conditions when no match is found
 *
 * @method     GemExchange findOneByExchangeDate(string $exchange_date) Return the first GemExchange filtered by the exchange_date column
 * @method     GemExchange findOneByExchangeTime(string $exchange_time) Return the first GemExchange filtered by the exchange_time column
 * @method     GemExchange findOneByAverage(int $average) Return the first GemExchange filtered by the average column
 *
 * @method     array findByExchangeDate(string $exchange_date) Return GemExchange objects filtered by the exchange_date column
 * @method     array findByExchangeTime(string $exchange_time) Return GemExchange objects filtered by the exchange_time column
 * @method     array findByAverage(int $average) Return GemExchange objects filtered by the average column
 *
 * @package    propel.generator.gw2spidy.om
 */
abstract class BaseGemExchangeQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseGemExchangeQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'gw2spidy', $modelName = 'GW2Spidy\\DB\\GemExchange', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new GemExchangeQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     GemExchangeQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return GemExchangeQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof GemExchangeQuery) {
            return $criteria;
        }
        $query = new GemExchangeQuery();
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
                         A Primary key composition: [$exchange_date, $exchange_time]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   GemExchange|GemExchange[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = GemExchangePeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(GemExchangePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   GemExchange A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `EXCHANGE_DATE`, `EXCHANGE_TIME`, `AVERAGE` FROM `gem_exchange` WHERE `EXCHANGE_DATE` = :p0 AND `EXCHANGE_TIME` = :p1';
        try {
            $stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key[0], PDO::PARAM_STR);
			$stmt->bindValue(':p1', $key[1], PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new GemExchange();
            $obj->hydrate($row);
            GemExchangePeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return GemExchange|GemExchange[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|GemExchange[]|mixed the list of results, formatted by the current formatter
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
     * @return GemExchangeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(GemExchangePeer::EXCHANGE_DATE, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(GemExchangePeer::EXCHANGE_TIME, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return GemExchangeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(GemExchangePeer::EXCHANGE_DATE, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(GemExchangePeer::EXCHANGE_TIME, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the exchange_date column
     *
     * Example usage:
     * <code>
     * $query->filterByExchangeDate('2011-03-14'); // WHERE exchange_date = '2011-03-14'
     * $query->filterByExchangeDate('now'); // WHERE exchange_date = '2011-03-14'
     * $query->filterByExchangeDate(array('max' => 'yesterday')); // WHERE exchange_date > '2011-03-13'
     * </code>
     *
     * @param     mixed $exchangeDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GemExchangeQuery The current query, for fluid interface
     */
    public function filterByExchangeDate($exchangeDate = null, $comparison = null)
    {
        if (is_array($exchangeDate)) {
            $useMinMax = false;
            if (isset($exchangeDate['min'])) {
                $this->addUsingAlias(GemExchangePeer::EXCHANGE_DATE, $exchangeDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($exchangeDate['max'])) {
                $this->addUsingAlias(GemExchangePeer::EXCHANGE_DATE, $exchangeDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GemExchangePeer::EXCHANGE_DATE, $exchangeDate, $comparison);
    }

    /**
     * Filter the query on the exchange_time column
     *
     * Example usage:
     * <code>
     * $query->filterByExchangeTime('2011-03-14'); // WHERE exchange_time = '2011-03-14'
     * $query->filterByExchangeTime('now'); // WHERE exchange_time = '2011-03-14'
     * $query->filterByExchangeTime(array('max' => 'yesterday')); // WHERE exchange_time > '2011-03-13'
     * </code>
     *
     * @param     mixed $exchangeTime The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GemExchangeQuery The current query, for fluid interface
     */
    public function filterByExchangeTime($exchangeTime = null, $comparison = null)
    {
        if (is_array($exchangeTime)) {
            $useMinMax = false;
            if (isset($exchangeTime['min'])) {
                $this->addUsingAlias(GemExchangePeer::EXCHANGE_TIME, $exchangeTime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($exchangeTime['max'])) {
                $this->addUsingAlias(GemExchangePeer::EXCHANGE_TIME, $exchangeTime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GemExchangePeer::EXCHANGE_TIME, $exchangeTime, $comparison);
    }

    /**
     * Filter the query on the average column
     *
     * Example usage:
     * <code>
     * $query->filterByAverage(1234); // WHERE average = 1234
     * $query->filterByAverage(array(12, 34)); // WHERE average IN (12, 34)
     * $query->filterByAverage(array('min' => 12)); // WHERE average > 12
     * </code>
     *
     * @param     mixed $average The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GemExchangeQuery The current query, for fluid interface
     */
    public function filterByAverage($average = null, $comparison = null)
    {
        if (is_array($average)) {
            $useMinMax = false;
            if (isset($average['min'])) {
                $this->addUsingAlias(GemExchangePeer::AVERAGE, $average['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($average['max'])) {
                $this->addUsingAlias(GemExchangePeer::AVERAGE, $average['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GemExchangePeer::AVERAGE, $average, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   GemExchange $gemExchange Object to remove from the list of results
     *
     * @return GemExchangeQuery The current query, for fluid interface
     */
    public function prune($gemExchange = null)
    {
        if ($gemExchange) {
            $this->addCond('pruneCond0', $this->getAliasedColName(GemExchangePeer::EXCHANGE_DATE), $gemExchange->getExchangeDate(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(GemExchangePeer::EXCHANGE_TIME), $gemExchange->getExchangeTime(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

} // BaseGemExchangeQuery