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
use GW2Spidy\DB\RequestFloodControl;
use GW2Spidy\DB\RequestFloodControlPeer;
use GW2Spidy\DB\RequestFloodControlQuery;

/**
 * Base class that represents a query for the 'request_flood_control' table.
 *
 * 
 *
 * @method     RequestFloodControlQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     RequestFloodControlQuery orderByHandlerUUID($order = Criteria::ASC) Order by the handler_uuid column
 * @method     RequestFloodControlQuery orderByTouched($order = Criteria::ASC) Order by the touched column
 *
 * @method     RequestFloodControlQuery groupById() Group by the id column
 * @method     RequestFloodControlQuery groupByHandlerUUID() Group by the handler_uuid column
 * @method     RequestFloodControlQuery groupByTouched() Group by the touched column
 *
 * @method     RequestFloodControlQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     RequestFloodControlQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     RequestFloodControlQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     RequestFloodControl findOne(PropelPDO $con = null) Return the first RequestFloodControl matching the query
 * @method     RequestFloodControl findOneOrCreate(PropelPDO $con = null) Return the first RequestFloodControl matching the query, or a new RequestFloodControl object populated from the query conditions when no match is found
 *
 * @method     RequestFloodControl findOneById(int $id) Return the first RequestFloodControl filtered by the id column
 * @method     RequestFloodControl findOneByHandlerUUID(string $handler_uuid) Return the first RequestFloodControl filtered by the handler_uuid column
 * @method     RequestFloodControl findOneByTouched(string $touched) Return the first RequestFloodControl filtered by the touched column
 *
 * @method     array findById(int $id) Return RequestFloodControl objects filtered by the id column
 * @method     array findByHandlerUUID(string $handler_uuid) Return RequestFloodControl objects filtered by the handler_uuid column
 * @method     array findByTouched(string $touched) Return RequestFloodControl objects filtered by the touched column
 *
 * @package    propel.generator.gw2spidy.om
 */
abstract class BaseRequestFloodControlQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseRequestFloodControlQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'gw2spidy', $modelName = 'GW2Spidy\\DB\\RequestFloodControl', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new RequestFloodControlQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     RequestFloodControlQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return RequestFloodControlQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof RequestFloodControlQuery) {
            return $criteria;
        }
        $query = new RequestFloodControlQuery();
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
     * @return   RequestFloodControl|RequestFloodControl[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = RequestFloodControlPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(RequestFloodControlPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   RequestFloodControl A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `ID`, `HANDLER_UUID`, `TOUCHED` FROM `request_flood_control` WHERE `ID` = :p0';
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
            $obj = new RequestFloodControl();
            $obj->hydrate($row);
            RequestFloodControlPeer::addInstanceToPool($obj, (string) $key);
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
     * @return RequestFloodControl|RequestFloodControl[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|RequestFloodControl[]|mixed the list of results, formatted by the current formatter
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
     * @return RequestFloodControlQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(RequestFloodControlPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return RequestFloodControlQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(RequestFloodControlPeer::ID, $keys, Criteria::IN);
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
     * @return RequestFloodControlQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(RequestFloodControlPeer::ID, $id, $comparison);
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
     * @return RequestFloodControlQuery The current query, for fluid interface
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

        return $this->addUsingAlias(RequestFloodControlPeer::HANDLER_UUID, $handlerUUID, $comparison);
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
     * @return RequestFloodControlQuery The current query, for fluid interface
     */
    public function filterByTouched($touched = null, $comparison = null)
    {
        if (is_array($touched)) {
            $useMinMax = false;
            if (isset($touched['min'])) {
                $this->addUsingAlias(RequestFloodControlPeer::TOUCHED, $touched['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($touched['max'])) {
                $this->addUsingAlias(RequestFloodControlPeer::TOUCHED, $touched['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RequestFloodControlPeer::TOUCHED, $touched, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   RequestFloodControl $requestFloodControl Object to remove from the list of results
     *
     * @return RequestFloodControlQuery The current query, for fluid interface
     */
    public function prune($requestFloodControl = null)
    {
        if ($requestFloodControl) {
            $this->addUsingAlias(RequestFloodControlPeer::ID, $requestFloodControl->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseRequestFloodControlQuery