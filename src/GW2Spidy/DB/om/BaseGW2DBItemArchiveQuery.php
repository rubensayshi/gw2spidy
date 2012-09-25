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
use GW2Spidy\DB\GW2DBItemArchive;
use GW2Spidy\DB\GW2DBItemArchivePeer;
use GW2Spidy\DB\GW2DBItemArchiveQuery;

/**
 * Base class that represents a query for the 'gw2db_item_archive' table.
 *
 * 
 *
 * @method     GW2DBItemArchiveQuery orderById($order = Criteria::ASC) Order by the ID column
 * @method     GW2DBItemArchiveQuery orderByExternalid($order = Criteria::ASC) Order by the ExternalID column
 * @method     GW2DBItemArchiveQuery orderByDataid($order = Criteria::ASC) Order by the DataID column
 * @method     GW2DBItemArchiveQuery orderByName($order = Criteria::ASC) Order by the Name column
 *
 * @method     GW2DBItemArchiveQuery groupById() Group by the ID column
 * @method     GW2DBItemArchiveQuery groupByExternalid() Group by the ExternalID column
 * @method     GW2DBItemArchiveQuery groupByDataid() Group by the DataID column
 * @method     GW2DBItemArchiveQuery groupByName() Group by the Name column
 *
 * @method     GW2DBItemArchiveQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     GW2DBItemArchiveQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     GW2DBItemArchiveQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     GW2DBItemArchive findOne(PropelPDO $con = null) Return the first GW2DBItemArchive matching the query
 * @method     GW2DBItemArchive findOneOrCreate(PropelPDO $con = null) Return the first GW2DBItemArchive matching the query, or a new GW2DBItemArchive object populated from the query conditions when no match is found
 *
 * @method     GW2DBItemArchive findOneById(int $ID) Return the first GW2DBItemArchive filtered by the ID column
 * @method     GW2DBItemArchive findOneByExternalid(int $ExternalID) Return the first GW2DBItemArchive filtered by the ExternalID column
 * @method     GW2DBItemArchive findOneByDataid(int $DataID) Return the first GW2DBItemArchive filtered by the DataID column
 * @method     GW2DBItemArchive findOneByName(string $Name) Return the first GW2DBItemArchive filtered by the Name column
 *
 * @method     array findById(int $ID) Return GW2DBItemArchive objects filtered by the ID column
 * @method     array findByExternalid(int $ExternalID) Return GW2DBItemArchive objects filtered by the ExternalID column
 * @method     array findByDataid(int $DataID) Return GW2DBItemArchive objects filtered by the DataID column
 * @method     array findByName(string $Name) Return GW2DBItemArchive objects filtered by the Name column
 *
 * @package    propel.generator.gw2spidy.om
 */
abstract class BaseGW2DBItemArchiveQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseGW2DBItemArchiveQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'gw2spidy', $modelName = 'GW2Spidy\\DB\\GW2DBItemArchive', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new GW2DBItemArchiveQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     GW2DBItemArchiveQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return GW2DBItemArchiveQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof GW2DBItemArchiveQuery) {
            return $criteria;
        }
        $query = new GW2DBItemArchiveQuery();
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
     * @return   GW2DBItemArchive|GW2DBItemArchive[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = GW2DBItemArchivePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(GW2DBItemArchivePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   GW2DBItemArchive A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `ID`, `EXTERNALID`, `DATAID`, `NAME` FROM `gw2db_item_archive` WHERE `ID` = :p0';
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
            $obj = new GW2DBItemArchive();
            $obj->hydrate($row);
            GW2DBItemArchivePeer::addInstanceToPool($obj, (string) $key);
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
     * @return GW2DBItemArchive|GW2DBItemArchive[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|GW2DBItemArchive[]|mixed the list of results, formatted by the current formatter
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
     * @return GW2DBItemArchiveQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(GW2DBItemArchivePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return GW2DBItemArchiveQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(GW2DBItemArchivePeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the ID column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE ID = 1234
     * $query->filterById(array(12, 34)); // WHERE ID IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE ID > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GW2DBItemArchiveQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(GW2DBItemArchivePeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the ExternalID column
     *
     * Example usage:
     * <code>
     * $query->filterByExternalid(1234); // WHERE ExternalID = 1234
     * $query->filterByExternalid(array(12, 34)); // WHERE ExternalID IN (12, 34)
     * $query->filterByExternalid(array('min' => 12)); // WHERE ExternalID > 12
     * </code>
     *
     * @param     mixed $externalid The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GW2DBItemArchiveQuery The current query, for fluid interface
     */
    public function filterByExternalid($externalid = null, $comparison = null)
    {
        if (is_array($externalid)) {
            $useMinMax = false;
            if (isset($externalid['min'])) {
                $this->addUsingAlias(GW2DBItemArchivePeer::EXTERNALID, $externalid['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($externalid['max'])) {
                $this->addUsingAlias(GW2DBItemArchivePeer::EXTERNALID, $externalid['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GW2DBItemArchivePeer::EXTERNALID, $externalid, $comparison);
    }

    /**
     * Filter the query on the DataID column
     *
     * Example usage:
     * <code>
     * $query->filterByDataid(1234); // WHERE DataID = 1234
     * $query->filterByDataid(array(12, 34)); // WHERE DataID IN (12, 34)
     * $query->filterByDataid(array('min' => 12)); // WHERE DataID > 12
     * </code>
     *
     * @param     mixed $dataid The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GW2DBItemArchiveQuery The current query, for fluid interface
     */
    public function filterByDataid($dataid = null, $comparison = null)
    {
        if (is_array($dataid)) {
            $useMinMax = false;
            if (isset($dataid['min'])) {
                $this->addUsingAlias(GW2DBItemArchivePeer::DATAID, $dataid['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dataid['max'])) {
                $this->addUsingAlias(GW2DBItemArchivePeer::DATAID, $dataid['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GW2DBItemArchivePeer::DATAID, $dataid, $comparison);
    }

    /**
     * Filter the query on the Name column
     *
     * Example usage:
     * <code>
     * $query->filterByName('fooValue');   // WHERE Name = 'fooValue'
     * $query->filterByName('%fooValue%'); // WHERE Name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $name The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GW2DBItemArchiveQuery The current query, for fluid interface
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

        return $this->addUsingAlias(GW2DBItemArchivePeer::NAME, $name, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   GW2DBItemArchive $gW2DBItemArchive Object to remove from the list of results
     *
     * @return GW2DBItemArchiveQuery The current query, for fluid interface
     */
    public function prune($gW2DBItemArchive = null)
    {
        if ($gW2DBItemArchive) {
            $this->addUsingAlias(GW2DBItemArchivePeer::ID, $gW2DBItemArchive->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseGW2DBItemArchiveQuery