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
use GW2Spidy\DB\TinyIntAsc;
use GW2Spidy\DB\TinyIntAscPeer;
use GW2Spidy\DB\TinyIntAscQuery;

/**
 * Base class that represents a query for the 'tinyint_asc' table.
 *
 * 
 *
 * @method     TinyIntAscQuery orderByValue($order = Criteria::ASC) Order by the value column
 *
 * @method     TinyIntAscQuery groupByValue() Group by the value column
 *
 * @method     TinyIntAscQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     TinyIntAscQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     TinyIntAscQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     TinyIntAsc findOne(PropelPDO $con = null) Return the first TinyIntAsc matching the query
 * @method     TinyIntAsc findOneOrCreate(PropelPDO $con = null) Return the first TinyIntAsc matching the query, or a new TinyIntAsc object populated from the query conditions when no match is found
 *
 * @method     TinyIntAsc findOneByValue(int $value) Return the first TinyIntAsc filtered by the value column
 *
 * @method     array findByValue(int $value) Return TinyIntAsc objects filtered by the value column
 *
 * @package    propel.generator.gw2spidy.om
 */
abstract class BaseTinyIntAscQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseTinyIntAscQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'gw2spidy', $modelName = 'GW2Spidy\\DB\\TinyIntAsc', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new TinyIntAscQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     TinyIntAscQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return TinyIntAscQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof TinyIntAscQuery) {
            return $criteria;
        }
        $query = new TinyIntAscQuery();
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
     * @return   TinyIntAsc|TinyIntAsc[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = TinyIntAscPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(TinyIntAscPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   TinyIntAsc A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `VALUE` FROM `tinyint_asc` WHERE `VALUE` = :p0';
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
            $obj = new TinyIntAsc();
            $obj->hydrate($row);
            TinyIntAscPeer::addInstanceToPool($obj, (string) $key);
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
     * @return TinyIntAsc|TinyIntAsc[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|TinyIntAsc[]|mixed the list of results, formatted by the current formatter
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
     * @return TinyIntAscQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(TinyIntAscPeer::VALUE, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return TinyIntAscQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(TinyIntAscPeer::VALUE, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the value column
     *
     * Example usage:
     * <code>
     * $query->filterByValue(1234); // WHERE value = 1234
     * $query->filterByValue(array(12, 34)); // WHERE value IN (12, 34)
     * $query->filterByValue(array('min' => 12)); // WHERE value > 12
     * </code>
     *
     * @param     mixed $value The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TinyIntAscQuery The current query, for fluid interface
     */
    public function filterByValue($value = null, $comparison = null)
    {
        if (is_array($value) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(TinyIntAscPeer::VALUE, $value, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   TinyIntAsc $tinyIntAsc Object to remove from the list of results
     *
     * @return TinyIntAscQuery The current query, for fluid interface
     */
    public function prune($tinyIntAsc = null)
    {
        if ($tinyIntAsc) {
            $this->addUsingAlias(TinyIntAscPeer::VALUE, $tinyIntAsc->getValue(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseTinyIntAscQuery