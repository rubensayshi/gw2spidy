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
use GW2Spidy\DB\SellGemRate;
use GW2Spidy\DB\SellGemRatePeer;
use GW2Spidy\DB\SellGemRateQuery;

/**
 * Base class that represents a query for the 'sell_gem_rate' table.
 *
 * 
 *
 * @method     SellGemRateQuery orderByRateDatetime($order = Criteria::ASC) Order by the rate_datetime column
 * @method     SellGemRateQuery orderByAverage($order = Criteria::ASC) Order by the average column
 *
 * @method     SellGemRateQuery groupByRateDatetime() Group by the rate_datetime column
 * @method     SellGemRateQuery groupByAverage() Group by the average column
 *
 * @method     SellGemRateQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     SellGemRateQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     SellGemRateQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     SellGemRate findOne(PropelPDO $con = null) Return the first SellGemRate matching the query
 * @method     SellGemRate findOneOrCreate(PropelPDO $con = null) Return the first SellGemRate matching the query, or a new SellGemRate object populated from the query conditions when no match is found
 *
 * @method     SellGemRate findOneByRateDatetime(string $rate_datetime) Return the first SellGemRate filtered by the rate_datetime column
 * @method     SellGemRate findOneByAverage(int $average) Return the first SellGemRate filtered by the average column
 *
 * @method     array findByRateDatetime(string $rate_datetime) Return SellGemRate objects filtered by the rate_datetime column
 * @method     array findByAverage(int $average) Return SellGemRate objects filtered by the average column
 *
 * @package    propel.generator.gw2spidy.om
 */
abstract class BaseSellGemRateQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseSellGemRateQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'gw2spidy', $modelName = 'GW2Spidy\\DB\\SellGemRate', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new SellGemRateQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     SellGemRateQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return SellGemRateQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof SellGemRateQuery) {
            return $criteria;
        }
        $query = new SellGemRateQuery();
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
     * @return   SellGemRate|SellGemRate[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SellGemRatePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(SellGemRatePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   SellGemRate A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `RATE_DATETIME`, `AVERAGE` FROM `sell_gem_rate` WHERE `RATE_DATETIME` = :p0';
        try {
            $stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key, PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new SellGemRate();
            $obj->hydrate($row);
            SellGemRatePeer::addInstanceToPool($obj, (string) $key);
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
     * @return SellGemRate|SellGemRate[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|SellGemRate[]|mixed the list of results, formatted by the current formatter
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
     * @return SellGemRateQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SellGemRatePeer::RATE_DATETIME, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return SellGemRateQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SellGemRatePeer::RATE_DATETIME, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the rate_datetime column
     *
     * Example usage:
     * <code>
     * $query->filterByRateDatetime('2011-03-14'); // WHERE rate_datetime = '2011-03-14'
     * $query->filterByRateDatetime('now'); // WHERE rate_datetime = '2011-03-14'
     * $query->filterByRateDatetime(array('max' => 'yesterday')); // WHERE rate_datetime > '2011-03-13'
     * </code>
     *
     * @param     mixed $rateDatetime The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SellGemRateQuery The current query, for fluid interface
     */
    public function filterByRateDatetime($rateDatetime = null, $comparison = null)
    {
        if (is_array($rateDatetime)) {
            $useMinMax = false;
            if (isset($rateDatetime['min'])) {
                $this->addUsingAlias(SellGemRatePeer::RATE_DATETIME, $rateDatetime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($rateDatetime['max'])) {
                $this->addUsingAlias(SellGemRatePeer::RATE_DATETIME, $rateDatetime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SellGemRatePeer::RATE_DATETIME, $rateDatetime, $comparison);
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
     * @return SellGemRateQuery The current query, for fluid interface
     */
    public function filterByAverage($average = null, $comparison = null)
    {
        if (is_array($average)) {
            $useMinMax = false;
            if (isset($average['min'])) {
                $this->addUsingAlias(SellGemRatePeer::AVERAGE, $average['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($average['max'])) {
                $this->addUsingAlias(SellGemRatePeer::AVERAGE, $average['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SellGemRatePeer::AVERAGE, $average, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   SellGemRate $sellGemRate Object to remove from the list of results
     *
     * @return SellGemRateQuery The current query, for fluid interface
     */
    public function prune($sellGemRate = null)
    {
        if ($sellGemRate) {
            $this->addUsingAlias(SellGemRatePeer::RATE_DATETIME, $sellGemRate->getRateDatetime(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseSellGemRateQuery