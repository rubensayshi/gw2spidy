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
use GW2Spidy\DB\RequestWorkerQueue;
use GW2Spidy\DB\RequestWorkerQueuePeer;
use GW2Spidy\DB\RequestWorkerQueueQuery;

/**
 * Base class that represents a query for the 'request_worker_queue' table.
 *
 * 
 *
 * @method     RequestWorkerQueueQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     RequestWorkerQueueQuery orderByPriority($order = Criteria::ASC) Order by the priority column
 * @method     RequestWorkerQueueQuery orderByStatus($order = Criteria::ASC) Order by the status column
 * @method     RequestWorkerQueueQuery orderByWorker($order = Criteria::ASC) Order by the worker column
 * @method     RequestWorkerQueueQuery orderByData($order = Criteria::ASC) Order by the data column
 * @method     RequestWorkerQueueQuery orderByHandlerUUID($order = Criteria::ASC) Order by the handler_uuid column
 * @method     RequestWorkerQueueQuery orderByTouched($order = Criteria::ASC) Order by the touched column
 * @method     RequestWorkerQueueQuery orderByMaxTimeout($order = Criteria::ASC) Order by the max_timeout column
 *
 * @method     RequestWorkerQueueQuery groupById() Group by the id column
 * @method     RequestWorkerQueueQuery groupByPriority() Group by the priority column
 * @method     RequestWorkerQueueQuery groupByStatus() Group by the status column
 * @method     RequestWorkerQueueQuery groupByWorker() Group by the worker column
 * @method     RequestWorkerQueueQuery groupByData() Group by the data column
 * @method     RequestWorkerQueueQuery groupByHandlerUUID() Group by the handler_uuid column
 * @method     RequestWorkerQueueQuery groupByTouched() Group by the touched column
 * @method     RequestWorkerQueueQuery groupByMaxTimeout() Group by the max_timeout column
 *
 * @method     RequestWorkerQueueQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     RequestWorkerQueueQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     RequestWorkerQueueQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     RequestWorkerQueue findOne(PropelPDO $con = null) Return the first RequestWorkerQueue matching the query
 * @method     RequestWorkerQueue findOneOrCreate(PropelPDO $con = null) Return the first RequestWorkerQueue matching the query, or a new RequestWorkerQueue object populated from the query conditions when no match is found
 *
 * @method     RequestWorkerQueue findOneById(int $id) Return the first RequestWorkerQueue filtered by the id column
 * @method     RequestWorkerQueue findOneByPriority(int $priority) Return the first RequestWorkerQueue filtered by the priority column
 * @method     RequestWorkerQueue findOneByStatus(string $status) Return the first RequestWorkerQueue filtered by the status column
 * @method     RequestWorkerQueue findOneByWorker(string $worker) Return the first RequestWorkerQueue filtered by the worker column
 * @method     RequestWorkerQueue findOneByData(string $data) Return the first RequestWorkerQueue filtered by the data column
 * @method     RequestWorkerQueue findOneByHandlerUUID(string $handler_uuid) Return the first RequestWorkerQueue filtered by the handler_uuid column
 * @method     RequestWorkerQueue findOneByTouched(string $touched) Return the first RequestWorkerQueue filtered by the touched column
 * @method     RequestWorkerQueue findOneByMaxTimeout(int $max_timeout) Return the first RequestWorkerQueue filtered by the max_timeout column
 *
 * @method     array findById(int $id) Return RequestWorkerQueue objects filtered by the id column
 * @method     array findByPriority(int $priority) Return RequestWorkerQueue objects filtered by the priority column
 * @method     array findByStatus(string $status) Return RequestWorkerQueue objects filtered by the status column
 * @method     array findByWorker(string $worker) Return RequestWorkerQueue objects filtered by the worker column
 * @method     array findByData(string $data) Return RequestWorkerQueue objects filtered by the data column
 * @method     array findByHandlerUUID(string $handler_uuid) Return RequestWorkerQueue objects filtered by the handler_uuid column
 * @method     array findByTouched(string $touched) Return RequestWorkerQueue objects filtered by the touched column
 * @method     array findByMaxTimeout(int $max_timeout) Return RequestWorkerQueue objects filtered by the max_timeout column
 *
 * @package    propel.generator.gw2spidy.om
 */
abstract class BaseRequestWorkerQueueQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseRequestWorkerQueueQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'gw2spidy', $modelName = 'GW2Spidy\\DB\\RequestWorkerQueue', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new RequestWorkerQueueQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     RequestWorkerQueueQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return RequestWorkerQueueQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof RequestWorkerQueueQuery) {
            return $criteria;
        }
        $query = new RequestWorkerQueueQuery();
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
     * @return   RequestWorkerQueue|RequestWorkerQueue[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = RequestWorkerQueuePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(RequestWorkerQueuePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   RequestWorkerQueue A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `ID`, `PRIORITY`, `STATUS`, `WORKER`, `DATA`, `HANDLER_UUID`, `TOUCHED`, `MAX_TIMEOUT` FROM `request_worker_queue` WHERE `ID` = :p0';
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
            $obj = new RequestWorkerQueue();
            $obj->hydrate($row);
            RequestWorkerQueuePeer::addInstanceToPool($obj, (string) $key);
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
     * @return RequestWorkerQueue|RequestWorkerQueue[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|RequestWorkerQueue[]|mixed the list of results, formatted by the current formatter
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
     * @return RequestWorkerQueueQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(RequestWorkerQueuePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return RequestWorkerQueueQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(RequestWorkerQueuePeer::ID, $keys, Criteria::IN);
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
     * @return RequestWorkerQueueQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(RequestWorkerQueuePeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the priority column
     *
     * Example usage:
     * <code>
     * $query->filterByPriority(1234); // WHERE priority = 1234
     * $query->filterByPriority(array(12, 34)); // WHERE priority IN (12, 34)
     * $query->filterByPriority(array('min' => 12)); // WHERE priority > 12
     * </code>
     *
     * @param     mixed $priority The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RequestWorkerQueueQuery The current query, for fluid interface
     */
    public function filterByPriority($priority = null, $comparison = null)
    {
        if (is_array($priority)) {
            $useMinMax = false;
            if (isset($priority['min'])) {
                $this->addUsingAlias(RequestWorkerQueuePeer::PRIORITY, $priority['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($priority['max'])) {
                $this->addUsingAlias(RequestWorkerQueuePeer::PRIORITY, $priority['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RequestWorkerQueuePeer::PRIORITY, $priority, $comparison);
    }

    /**
     * Filter the query on the status column
     *
     * Example usage:
     * <code>
     * $query->filterByStatus('fooValue');   // WHERE status = 'fooValue'
     * $query->filterByStatus('%fooValue%'); // WHERE status LIKE '%fooValue%'
     * </code>
     *
     * @param     string $status The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RequestWorkerQueueQuery The current query, for fluid interface
     */
    public function filterByStatus($status = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($status)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $status)) {
                $status = str_replace('*', '%', $status);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(RequestWorkerQueuePeer::STATUS, $status, $comparison);
    }

    /**
     * Filter the query on the worker column
     *
     * Example usage:
     * <code>
     * $query->filterByWorker('fooValue');   // WHERE worker = 'fooValue'
     * $query->filterByWorker('%fooValue%'); // WHERE worker LIKE '%fooValue%'
     * </code>
     *
     * @param     string $worker The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RequestWorkerQueueQuery The current query, for fluid interface
     */
    public function filterByWorker($worker = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($worker)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $worker)) {
                $worker = str_replace('*', '%', $worker);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(RequestWorkerQueuePeer::WORKER, $worker, $comparison);
    }

    /**
     * Filter the query on the data column
     *
     * Example usage:
     * <code>
     * $query->filterByData('fooValue');   // WHERE data = 'fooValue'
     * $query->filterByData('%fooValue%'); // WHERE data LIKE '%fooValue%'
     * </code>
     *
     * @param     string $data The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RequestWorkerQueueQuery The current query, for fluid interface
     */
    public function filterByData($data = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($data)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $data)) {
                $data = str_replace('*', '%', $data);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(RequestWorkerQueuePeer::DATA, $data, $comparison);
    }

    /**
     * Filter the query on the handler_uuid column
     *
     * Example usage:
     * <code>
     * $query->filterByHandlerUUID('fooValue');   // WHERE handler_uuid = 'fooValue'
     * $query->filterByHandlerUUID('%fooValue%'); // WHERE handler_uuid LIKE '%fooValue%'
     * </code>
     *
     * @param     string $handlerUUID The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RequestWorkerQueueQuery The current query, for fluid interface
     */
    public function filterByHandlerUUID($handlerUUID = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($handlerUUID)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $handlerUUID)) {
                $handlerUUID = str_replace('*', '%', $handlerUUID);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(RequestWorkerQueuePeer::HANDLER_UUID, $handlerUUID, $comparison);
    }

    /**
     * Filter the query on the touched column
     *
     * Example usage:
     * <code>
     * $query->filterByTouched('2011-03-14'); // WHERE touched = '2011-03-14'
     * $query->filterByTouched('now'); // WHERE touched = '2011-03-14'
     * $query->filterByTouched(array('max' => 'yesterday')); // WHERE touched > '2011-03-13'
     * </code>
     *
     * @param     mixed $touched The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RequestWorkerQueueQuery The current query, for fluid interface
     */
    public function filterByTouched($touched = null, $comparison = null)
    {
        if (is_array($touched)) {
            $useMinMax = false;
            if (isset($touched['min'])) {
                $this->addUsingAlias(RequestWorkerQueuePeer::TOUCHED, $touched['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($touched['max'])) {
                $this->addUsingAlias(RequestWorkerQueuePeer::TOUCHED, $touched['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RequestWorkerQueuePeer::TOUCHED, $touched, $comparison);
    }

    /**
     * Filter the query on the max_timeout column
     *
     * Example usage:
     * <code>
     * $query->filterByMaxTimeout(1234); // WHERE max_timeout = 1234
     * $query->filterByMaxTimeout(array(12, 34)); // WHERE max_timeout IN (12, 34)
     * $query->filterByMaxTimeout(array('min' => 12)); // WHERE max_timeout > 12
     * </code>
     *
     * @param     mixed $maxTimeout The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RequestWorkerQueueQuery The current query, for fluid interface
     */
    public function filterByMaxTimeout($maxTimeout = null, $comparison = null)
    {
        if (is_array($maxTimeout)) {
            $useMinMax = false;
            if (isset($maxTimeout['min'])) {
                $this->addUsingAlias(RequestWorkerQueuePeer::MAX_TIMEOUT, $maxTimeout['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($maxTimeout['max'])) {
                $this->addUsingAlias(RequestWorkerQueuePeer::MAX_TIMEOUT, $maxTimeout['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RequestWorkerQueuePeer::MAX_TIMEOUT, $maxTimeout, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   RequestWorkerQueue $requestWorkerQueue Object to remove from the list of results
     *
     * @return RequestWorkerQueueQuery The current query, for fluid interface
     */
    public function prune($requestWorkerQueue = null)
    {
        if ($requestWorkerQueue) {
            $this->addUsingAlias(RequestWorkerQueuePeer::ID, $requestWorkerQueue->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseRequestWorkerQueueQuery