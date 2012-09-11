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
use GW2Spidy\DB\GW2Session;
use GW2Spidy\DB\GW2SessionPeer;
use GW2Spidy\DB\GW2SessionQuery;

/**
 * Base class that represents a query for the 'gw2session' table.
 *
 * 
 *
 * @method     GW2SessionQuery orderBySessionKey($order = Criteria::ASC) Order by the session_key column
 * @method     GW2SessionQuery orderByGameSession($order = Criteria::ASC) Order by the game_session column
 * @method     GW2SessionQuery orderByCreated($order = Criteria::ASC) Order by the created column
 * @method     GW2SessionQuery orderBySource($order = Criteria::ASC) Order by the source column
 *
 * @method     GW2SessionQuery groupBySessionKey() Group by the session_key column
 * @method     GW2SessionQuery groupByGameSession() Group by the game_session column
 * @method     GW2SessionQuery groupByCreated() Group by the created column
 * @method     GW2SessionQuery groupBySource() Group by the source column
 *
 * @method     GW2SessionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     GW2SessionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     GW2SessionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     GW2Session findOne(PropelPDO $con = null) Return the first GW2Session matching the query
 * @method     GW2Session findOneOrCreate(PropelPDO $con = null) Return the first GW2Session matching the query, or a new GW2Session object populated from the query conditions when no match is found
 *
 * @method     GW2Session findOneBySessionKey(string $session_key) Return the first GW2Session filtered by the session_key column
 * @method     GW2Session findOneByGameSession(boolean $game_session) Return the first GW2Session filtered by the game_session column
 * @method     GW2Session findOneByCreated(string $created) Return the first GW2Session filtered by the created column
 * @method     GW2Session findOneBySource(string $source) Return the first GW2Session filtered by the source column
 *
 * @method     array findBySessionKey(string $session_key) Return GW2Session objects filtered by the session_key column
 * @method     array findByGameSession(boolean $game_session) Return GW2Session objects filtered by the game_session column
 * @method     array findByCreated(string $created) Return GW2Session objects filtered by the created column
 * @method     array findBySource(string $source) Return GW2Session objects filtered by the source column
 *
 * @package    propel.generator.gw2spidy.om
 */
abstract class BaseGW2SessionQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseGW2SessionQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'gw2spidy', $modelName = 'GW2Spidy\\DB\\GW2Session', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new GW2SessionQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     GW2SessionQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return GW2SessionQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof GW2SessionQuery) {
            return $criteria;
        }
        $query = new GW2SessionQuery();
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
     * @return   GW2Session|GW2Session[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = GW2SessionPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(GW2SessionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   GW2Session A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `SESSION_KEY`, `GAME_SESSION`, `CREATED`, `SOURCE` FROM `gw2session` WHERE `SESSION_KEY` = :p0';
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
            $obj = new GW2Session();
            $obj->hydrate($row);
            GW2SessionPeer::addInstanceToPool($obj, (string) $key);
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
     * @return GW2Session|GW2Session[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|GW2Session[]|mixed the list of results, formatted by the current formatter
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
     * @return GW2SessionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(GW2SessionPeer::SESSION_KEY, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return GW2SessionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(GW2SessionPeer::SESSION_KEY, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the session_key column
     *
     * Example usage:
     * <code>
     * $query->filterBySessionKey('fooValue');   // WHERE session_key = 'fooValue'
     * $query->filterBySessionKey('%fooValue%'); // WHERE session_key LIKE '%fooValue%'
     * </code>
     *
     * @param     string $sessionKey The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GW2SessionQuery The current query, for fluid interface
     */
    public function filterBySessionKey($sessionKey = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($sessionKey)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $sessionKey)) {
                $sessionKey = str_replace('*', '%', $sessionKey);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(GW2SessionPeer::SESSION_KEY, $sessionKey, $comparison);
    }

    /**
     * Filter the query on the game_session column
     *
     * Example usage:
     * <code>
     * $query->filterByGameSession(true); // WHERE game_session = true
     * $query->filterByGameSession('yes'); // WHERE game_session = true
     * </code>
     *
     * @param     boolean|string $gameSession The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GW2SessionQuery The current query, for fluid interface
     */
    public function filterByGameSession($gameSession = null, $comparison = null)
    {
        if (is_string($gameSession)) {
            $game_session = in_array(strtolower($gameSession), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(GW2SessionPeer::GAME_SESSION, $gameSession, $comparison);
    }

    /**
     * Filter the query on the created column
     *
     * Example usage:
     * <code>
     * $query->filterByCreated('2011-03-14'); // WHERE created = '2011-03-14'
     * $query->filterByCreated('now'); // WHERE created = '2011-03-14'
     * $query->filterByCreated(array('max' => 'yesterday')); // WHERE created > '2011-03-13'
     * </code>
     *
     * @param     mixed $created The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GW2SessionQuery The current query, for fluid interface
     */
    public function filterByCreated($created = null, $comparison = null)
    {
        if (is_array($created)) {
            $useMinMax = false;
            if (isset($created['min'])) {
                $this->addUsingAlias(GW2SessionPeer::CREATED, $created['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($created['max'])) {
                $this->addUsingAlias(GW2SessionPeer::CREATED, $created['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GW2SessionPeer::CREATED, $created, $comparison);
    }

    /**
     * Filter the query on the source column
     *
     * Example usage:
     * <code>
     * $query->filterBySource('fooValue');   // WHERE source = 'fooValue'
     * $query->filterBySource('%fooValue%'); // WHERE source LIKE '%fooValue%'
     * </code>
     *
     * @param     string $source The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GW2SessionQuery The current query, for fluid interface
     */
    public function filterBySource($source = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($source)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $source)) {
                $source = str_replace('*', '%', $source);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(GW2SessionPeer::SOURCE, $source, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   GW2Session $gW2Session Object to remove from the list of results
     *
     * @return GW2SessionQuery The current query, for fluid interface
     */
    public function prune($gW2Session = null)
    {
        if ($gW2Session) {
            $this->addUsingAlias(GW2SessionPeer::SESSION_KEY, $gW2Session->getSessionKey(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseGW2SessionQuery