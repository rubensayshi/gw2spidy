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
use GW2Spidy\DB\User;
use GW2Spidy\DB\UserPeer;
use GW2Spidy\DB\UserQuery;
use GW2Spidy\DB\Watchlist;

/**
 * Base class that represents a query for the 'user' table.
 *
 * 
 *
 * @method     UserQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     UserQuery orderByUsername($order = Criteria::ASC) Order by the username column
 * @method     UserQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method     UserQuery orderByPassword($order = Criteria::ASC) Order by the password column
 * @method     UserQuery orderByRoles($order = Criteria::ASC) Order by the roles column
 * @method     UserQuery orderByHybridAuthProviderId($order = Criteria::ASC) Order by the hybrid_auth_provider_id column
 * @method     UserQuery orderByHybridAuthId($order = Criteria::ASC) Order by the hybrid_auth_id column
 * @method     UserQuery orderByResetPassword($order = Criteria::ASC) Order by the reset_password column
 *
 * @method     UserQuery groupById() Group by the id column
 * @method     UserQuery groupByUsername() Group by the username column
 * @method     UserQuery groupByEmail() Group by the email column
 * @method     UserQuery groupByPassword() Group by the password column
 * @method     UserQuery groupByRoles() Group by the roles column
 * @method     UserQuery groupByHybridAuthProviderId() Group by the hybrid_auth_provider_id column
 * @method     UserQuery groupByHybridAuthId() Group by the hybrid_auth_id column
 * @method     UserQuery groupByResetPassword() Group by the reset_password column
 *
 * @method     UserQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     UserQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     UserQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     UserQuery leftJoinWatchlist($relationAlias = null) Adds a LEFT JOIN clause to the query using the Watchlist relation
 * @method     UserQuery rightJoinWatchlist($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Watchlist relation
 * @method     UserQuery innerJoinWatchlist($relationAlias = null) Adds a INNER JOIN clause to the query using the Watchlist relation
 *
 * @method     User findOne(PropelPDO $con = null) Return the first User matching the query
 * @method     User findOneOrCreate(PropelPDO $con = null) Return the first User matching the query, or a new User object populated from the query conditions when no match is found
 *
 * @method     User findOneById(int $id) Return the first User filtered by the id column
 * @method     User findOneByUsername(string $username) Return the first User filtered by the username column
 * @method     User findOneByEmail(string $email) Return the first User filtered by the email column
 * @method     User findOneByPassword(string $password) Return the first User filtered by the password column
 * @method     User findOneByRoles(string $roles) Return the first User filtered by the roles column
 * @method     User findOneByHybridAuthProviderId(string $hybrid_auth_provider_id) Return the first User filtered by the hybrid_auth_provider_id column
 * @method     User findOneByHybridAuthId(string $hybrid_auth_id) Return the first User filtered by the hybrid_auth_id column
 * @method     User findOneByResetPassword(string $reset_password) Return the first User filtered by the reset_password column
 *
 * @method     array findById(int $id) Return User objects filtered by the id column
 * @method     array findByUsername(string $username) Return User objects filtered by the username column
 * @method     array findByEmail(string $email) Return User objects filtered by the email column
 * @method     array findByPassword(string $password) Return User objects filtered by the password column
 * @method     array findByRoles(string $roles) Return User objects filtered by the roles column
 * @method     array findByHybridAuthProviderId(string $hybrid_auth_provider_id) Return User objects filtered by the hybrid_auth_provider_id column
 * @method     array findByHybridAuthId(string $hybrid_auth_id) Return User objects filtered by the hybrid_auth_id column
 * @method     array findByResetPassword(string $reset_password) Return User objects filtered by the reset_password column
 *
 * @package    propel.generator.gw2spidy.om
 */
abstract class BaseUserQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseUserQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'gw2spidy', $modelName = 'GW2Spidy\\DB\\User', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new UserQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     UserQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return UserQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof UserQuery) {
            return $criteria;
        }
        $query = new UserQuery();
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
     * @return   User|User[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = UserPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(UserPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   User A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `ID`, `USERNAME`, `EMAIL`, `PASSWORD`, `ROLES`, `HYBRID_AUTH_PROVIDER_ID`, `HYBRID_AUTH_ID`, `RESET_PASSWORD` FROM `user` WHERE `ID` = :p0';
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
            $obj = new User();
            $obj->hydrate($row);
            UserPeer::addInstanceToPool($obj, (string) $key);
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
     * @return User|User[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|User[]|mixed the list of results, formatted by the current formatter
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
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(UserPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(UserPeer::ID, $keys, Criteria::IN);
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
     * @return UserQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(UserPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the username column
     *
     * Example usage:
     * <code>
     * $query->filterByUsername('fooValue');   // WHERE username = 'fooValue'
     * $query->filterByUsername('%fooValue%'); // WHERE username LIKE '%fooValue%'
     * </code>
     *
     * @param     string $username The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByUsername($username = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($username)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $username)) {
                $username = str_replace('*', '%', $username);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserPeer::USERNAME, $username, $comparison);
    }

    /**
     * Filter the query on the email column
     *
     * Example usage:
     * <code>
     * $query->filterByEmail('fooValue');   // WHERE email = 'fooValue'
     * $query->filterByEmail('%fooValue%'); // WHERE email LIKE '%fooValue%'
     * </code>
     *
     * @param     string $email The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByEmail($email = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($email)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $email)) {
                $email = str_replace('*', '%', $email);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserPeer::EMAIL, $email, $comparison);
    }

    /**
     * Filter the query on the password column
     *
     * Example usage:
     * <code>
     * $query->filterByPassword('fooValue');   // WHERE password = 'fooValue'
     * $query->filterByPassword('%fooValue%'); // WHERE password LIKE '%fooValue%'
     * </code>
     *
     * @param     string $password The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByPassword($password = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($password)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $password)) {
                $password = str_replace('*', '%', $password);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserPeer::PASSWORD, $password, $comparison);
    }

    /**
     * Filter the query on the roles column
     *
     * Example usage:
     * <code>
     * $query->filterByRoles('fooValue');   // WHERE roles = 'fooValue'
     * $query->filterByRoles('%fooValue%'); // WHERE roles LIKE '%fooValue%'
     * </code>
     *
     * @param     string $roles The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByRoles($roles = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($roles)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $roles)) {
                $roles = str_replace('*', '%', $roles);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserPeer::ROLES, $roles, $comparison);
    }

    /**
     * Filter the query on the hybrid_auth_provider_id column
     *
     * Example usage:
     * <code>
     * $query->filterByHybridAuthProviderId('fooValue');   // WHERE hybrid_auth_provider_id = 'fooValue'
     * $query->filterByHybridAuthProviderId('%fooValue%'); // WHERE hybrid_auth_provider_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $hybridAuthProviderId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByHybridAuthProviderId($hybridAuthProviderId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($hybridAuthProviderId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $hybridAuthProviderId)) {
                $hybridAuthProviderId = str_replace('*', '%', $hybridAuthProviderId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserPeer::HYBRID_AUTH_PROVIDER_ID, $hybridAuthProviderId, $comparison);
    }

    /**
     * Filter the query on the hybrid_auth_id column
     *
     * Example usage:
     * <code>
     * $query->filterByHybridAuthId('fooValue');   // WHERE hybrid_auth_id = 'fooValue'
     * $query->filterByHybridAuthId('%fooValue%'); // WHERE hybrid_auth_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $hybridAuthId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByHybridAuthId($hybridAuthId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($hybridAuthId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $hybridAuthId)) {
                $hybridAuthId = str_replace('*', '%', $hybridAuthId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserPeer::HYBRID_AUTH_ID, $hybridAuthId, $comparison);
    }

    /**
     * Filter the query on the reset_password column
     *
     * Example usage:
     * <code>
     * $query->filterByResetPassword('fooValue');   // WHERE reset_password = 'fooValue'
     * $query->filterByResetPassword('%fooValue%'); // WHERE reset_password LIKE '%fooValue%'
     * </code>
     *
     * @param     string $resetPassword The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByResetPassword($resetPassword = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($resetPassword)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $resetPassword)) {
                $resetPassword = str_replace('*', '%', $resetPassword);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserPeer::RESET_PASSWORD, $resetPassword, $comparison);
    }

    /**
     * Filter the query by a related Watchlist object
     *
     * @param   Watchlist|PropelObjectCollection $watchlist  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   UserQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByWatchlist($watchlist, $comparison = null)
    {
        if ($watchlist instanceof Watchlist) {
            return $this
                ->addUsingAlias(UserPeer::ID, $watchlist->getUserId(), $comparison);
        } elseif ($watchlist instanceof PropelObjectCollection) {
            return $this
                ->useWatchlistQuery()
                ->filterByPrimaryKeys($watchlist->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByWatchlist() only accepts arguments of type Watchlist or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Watchlist relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function joinWatchlist($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Watchlist');

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
            $this->addJoinObject($join, 'Watchlist');
        }

        return $this;
    }

    /**
     * Use the Watchlist relation Watchlist object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \GW2Spidy\DB\WatchlistQuery A secondary query class using the current class as primary query
     */
    public function useWatchlistQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinWatchlist($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Watchlist', '\GW2Spidy\DB\WatchlistQuery');
    }

    /**
     * Filter the query by a related Item object
     * using the watchlist table as cross reference
     *
     * @param   Item $item the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   UserQuery The current query, for fluid interface
     */
    public function filterByItem($item, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useWatchlistQuery()
            ->filterByItem($item, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   User $user Object to remove from the list of results
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function prune($user = null)
    {
        if ($user) {
            $this->addUsingAlias(UserPeer::ID, $user->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseUserQuery