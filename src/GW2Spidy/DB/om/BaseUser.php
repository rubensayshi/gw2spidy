<?php

namespace GW2Spidy\DB\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\User;
use GW2Spidy\DB\UserPeer;
use GW2Spidy\DB\UserQuery;
use GW2Spidy\DB\Watchlist;
use GW2Spidy\DB\WatchlistQuery;

/**
 * Base class that represents a row from the 'user' table.
 *
 * 
 *
 * @package    propel.generator.gw2spidy.om
 */
abstract class BaseUser extends BaseObject implements Persistent
{

    /**
     * Peer class name
     */
    const PEER = 'GW2Spidy\\DB\\UserPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        UserPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinit loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the username field.
     * @var        string
     */
    protected $username;

    /**
     * The value for the email field.
     * @var        string
     */
    protected $email;

    /**
     * The value for the password field.
     * @var        string
     */
    protected $password;

    /**
     * The value for the roles field.
     * Note: this column has a database default value of: 'USER_ROLE'
     * @var        string
     */
    protected $roles;

    /**
     * The value for the hybrid_auth_provider_id field.
     * @var        string
     */
    protected $hybrid_auth_provider_id;

    /**
     * The value for the hybrid_auth_id field.
     * @var        string
     */
    protected $hybrid_auth_id;

    /**
     * The value for the reset_password field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $reset_password;

    /**
     * @var        PropelObjectCollection|Watchlist[] Collection to store aggregation of Watchlist objects.
     */
    protected $collWatchlists;
    protected $collWatchlistsPartial;

    /**
     * @var        PropelObjectCollection|Item[] Collection to store aggregation of Item objects.
     */
    protected $collItems;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $itemsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $watchlistsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->roles = 'USER_ROLE';
        $this->reset_password = '';
    }

    /**
     * Initializes internal state of BaseUser object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [id] column value.
     * 
     * @return   int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [username] column value.
     * 
     * @return   string
     */
    public function getUsername()
    {

        return $this->username;
    }

    /**
     * Get the [email] column value.
     * 
     * @return   string
     */
    public function getEmail()
    {

        return $this->email;
    }

    /**
     * Get the [password] column value.
     * 
     * @return   string
     */
    public function getPassword()
    {

        return $this->password;
    }

    /**
     * Get the [roles] column value.
     * 
     * @return   string
     */
    public function getRoles()
    {

        return $this->roles;
    }

    /**
     * Get the [hybrid_auth_provider_id] column value.
     * 
     * @return   string
     */
    public function getHybridAuthProviderId()
    {

        return $this->hybrid_auth_provider_id;
    }

    /**
     * Get the [hybrid_auth_id] column value.
     * 
     * @return   string
     */
    public function getHybridAuthId()
    {

        return $this->hybrid_auth_id;
    }

    /**
     * Get the [reset_password] column value.
     * 
     * @return   string
     */
    public function getResetPassword()
    {

        return $this->reset_password;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = UserPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [username] column.
     * 
     * @param      string $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setUsername($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->username !== $v) {
            $this->username = $v;
            $this->modifiedColumns[] = UserPeer::USERNAME;
        }


        return $this;
    } // setUsername()

    /**
     * Set the value of [email] column.
     * 
     * @param      string $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setEmail($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->email !== $v) {
            $this->email = $v;
            $this->modifiedColumns[] = UserPeer::EMAIL;
        }


        return $this;
    } // setEmail()

    /**
     * Set the value of [password] column.
     * 
     * @param      string $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setPassword($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->password !== $v) {
            $this->password = $v;
            $this->modifiedColumns[] = UserPeer::PASSWORD;
        }


        return $this;
    } // setPassword()

    /**
     * Set the value of [roles] column.
     * 
     * @param      string $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setRoles($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->roles !== $v) {
            $this->roles = $v;
            $this->modifiedColumns[] = UserPeer::ROLES;
        }


        return $this;
    } // setRoles()

    /**
     * Set the value of [hybrid_auth_provider_id] column.
     * 
     * @param      string $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setHybridAuthProviderId($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->hybrid_auth_provider_id !== $v) {
            $this->hybrid_auth_provider_id = $v;
            $this->modifiedColumns[] = UserPeer::HYBRID_AUTH_PROVIDER_ID;
        }


        return $this;
    } // setHybridAuthProviderId()

    /**
     * Set the value of [hybrid_auth_id] column.
     * 
     * @param      string $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setHybridAuthId($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->hybrid_auth_id !== $v) {
            $this->hybrid_auth_id = $v;
            $this->modifiedColumns[] = UserPeer::HYBRID_AUTH_ID;
        }


        return $this;
    } // setHybridAuthId()

    /**
     * Set the value of [reset_password] column.
     * 
     * @param      string $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setResetPassword($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->reset_password !== $v) {
            $this->reset_password = $v;
            $this->modifiedColumns[] = UserPeer::RESET_PASSWORD;
        }


        return $this;
    } // setResetPassword()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
            if ($this->roles !== 'USER_ROLE') {
                return false;
            }

            if ($this->reset_password !== '') {
                return false;
            }

        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param      array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param      int $startcol 0-based offset column which indicates which restultset column to start with.
     * @param      boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->username = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->email = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->password = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->roles = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->hybrid_auth_provider_id = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->hybrid_auth_id = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->reset_password = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 8; // 8 = UserPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating User object", $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {

    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(UserPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = UserPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collWatchlists = null;

            $this->collItems = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(UserPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = UserQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(UserPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                UserPeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->itemsScheduledForDeletion !== null) {
                if (!$this->itemsScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->itemsScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($pk, $remotePk);
                    }
                    WatchlistQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->itemsScheduledForDeletion = null;
                }

                foreach ($this->getItems() as $item) {
                    if ($item->isModified()) {
                        $item->save($con);
                    }
                }
            }

            if ($this->watchlistsScheduledForDeletion !== null) {
                if (!$this->watchlistsScheduledForDeletion->isEmpty()) {
                    WatchlistQuery::create()
                        ->filterByPrimaryKeys($this->watchlistsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->watchlistsScheduledForDeletion = null;
                }
            }

            if ($this->collWatchlists !== null) {
                foreach ($this->collWatchlists as $referrerFK) {
                    if (!$referrerFK->isDeleted()) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = UserPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . UserPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(UserPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`ID`';
        }
        if ($this->isColumnModified(UserPeer::USERNAME)) {
            $modifiedColumns[':p' . $index++]  = '`USERNAME`';
        }
        if ($this->isColumnModified(UserPeer::EMAIL)) {
            $modifiedColumns[':p' . $index++]  = '`EMAIL`';
        }
        if ($this->isColumnModified(UserPeer::PASSWORD)) {
            $modifiedColumns[':p' . $index++]  = '`PASSWORD`';
        }
        if ($this->isColumnModified(UserPeer::ROLES)) {
            $modifiedColumns[':p' . $index++]  = '`ROLES`';
        }
        if ($this->isColumnModified(UserPeer::HYBRID_AUTH_PROVIDER_ID)) {
            $modifiedColumns[':p' . $index++]  = '`HYBRID_AUTH_PROVIDER_ID`';
        }
        if ($this->isColumnModified(UserPeer::HYBRID_AUTH_ID)) {
            $modifiedColumns[':p' . $index++]  = '`HYBRID_AUTH_ID`';
        }
        if ($this->isColumnModified(UserPeer::RESET_PASSWORD)) {
            $modifiedColumns[':p' . $index++]  = '`RESET_PASSWORD`';
        }

        $sql = sprintf(
            'INSERT INTO `user` (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '`ID`':
						$stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case '`USERNAME`':
						$stmt->bindValue($identifier, $this->username, PDO::PARAM_STR);
                        break;
                    case '`EMAIL`':
						$stmt->bindValue($identifier, $this->email, PDO::PARAM_STR);
                        break;
                    case '`PASSWORD`':
						$stmt->bindValue($identifier, $this->password, PDO::PARAM_STR);
                        break;
                    case '`ROLES`':
						$stmt->bindValue($identifier, $this->roles, PDO::PARAM_STR);
                        break;
                    case '`HYBRID_AUTH_PROVIDER_ID`':
						$stmt->bindValue($identifier, $this->hybrid_auth_provider_id, PDO::PARAM_STR);
                        break;
                    case '`HYBRID_AUTH_ID`':
						$stmt->bindValue($identifier, $this->hybrid_auth_id, PDO::PARAM_STR);
                        break;
                    case '`RESET_PASSWORD`':
						$stmt->bindValue($identifier, $this->reset_password, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        try {
			$pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param      mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        } else {
            $this->validationFailures = $res;

            return false;
        }
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggreagated array of ValidationFailed objects will be returned.
     *
     * @param      array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            if (($retval = UserPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collWatchlists !== null) {
                    foreach ($this->collWatchlists as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }


            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = UserPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getUsername();
                break;
            case 2:
                return $this->getEmail();
                break;
            case 3:
                return $this->getPassword();
                break;
            case 4:
                return $this->getRoles();
                break;
            case 5:
                return $this->getHybridAuthProviderId();
                break;
            case 6:
                return $this->getHybridAuthId();
                break;
            case 7:
                return $this->getResetPassword();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                    Defaults to BasePeer::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['User'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['User'][$this->getPrimaryKey()] = true;
        $keys = UserPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getUsername(),
            $keys[2] => $this->getEmail(),
            $keys[3] => $this->getPassword(),
            $keys[4] => $this->getRoles(),
            $keys[5] => $this->getHybridAuthProviderId(),
            $keys[6] => $this->getHybridAuthId(),
            $keys[7] => $this->getResetPassword(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->collWatchlists) {
                $result['Watchlists'] = $this->collWatchlists->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param      string $name peer name
     * @param      mixed $value field value
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = UserPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

        $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @param      mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setUsername($value);
                break;
            case 2:
                $this->setEmail($value);
                break;
            case 3:
                $this->setPassword($value);
                break;
            case 4:
                $this->setRoles($value);
                break;
            case 5:
                $this->setHybridAuthProviderId($value);
                break;
            case 6:
                $this->setHybridAuthId($value);
                break;
            case 7:
                $this->setResetPassword($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     * The default key type is the column's BasePeer::TYPE_PHPNAME
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = UserPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setUsername($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setEmail($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setPassword($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setRoles($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setHybridAuthProviderId($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setHybridAuthId($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setResetPassword($arr[$keys[7]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(UserPeer::DATABASE_NAME);

        if ($this->isColumnModified(UserPeer::ID)) $criteria->add(UserPeer::ID, $this->id);
        if ($this->isColumnModified(UserPeer::USERNAME)) $criteria->add(UserPeer::USERNAME, $this->username);
        if ($this->isColumnModified(UserPeer::EMAIL)) $criteria->add(UserPeer::EMAIL, $this->email);
        if ($this->isColumnModified(UserPeer::PASSWORD)) $criteria->add(UserPeer::PASSWORD, $this->password);
        if ($this->isColumnModified(UserPeer::ROLES)) $criteria->add(UserPeer::ROLES, $this->roles);
        if ($this->isColumnModified(UserPeer::HYBRID_AUTH_PROVIDER_ID)) $criteria->add(UserPeer::HYBRID_AUTH_PROVIDER_ID, $this->hybrid_auth_provider_id);
        if ($this->isColumnModified(UserPeer::HYBRID_AUTH_ID)) $criteria->add(UserPeer::HYBRID_AUTH_ID, $this->hybrid_auth_id);
        if ($this->isColumnModified(UserPeer::RESET_PASSWORD)) $criteria->add(UserPeer::RESET_PASSWORD, $this->reset_password);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(UserPeer::DATABASE_NAME);
        $criteria->add(UserPeer::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return   int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of User (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setUsername($this->getUsername());
        $copyObj->setEmail($this->getEmail());
        $copyObj->setPassword($this->getPassword());
        $copyObj->setRoles($this->getRoles());
        $copyObj->setHybridAuthProviderId($this->getHybridAuthProviderId());
        $copyObj->setHybridAuthId($this->getHybridAuthId());
        $copyObj->setResetPassword($this->getResetPassword());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getWatchlists() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addWatchlist($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return                 User Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return   UserPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new UserPeer();
        }

        return self::$peer;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('Watchlist' == $relationName) {
            $this->initWatchlists();
        }
    }

    /**
     * Clears out the collWatchlists collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addWatchlists()
     */
    public function clearWatchlists()
    {
        $this->collWatchlists = null; // important to set this to NULL since that means it is uninitialized
        $this->collWatchlistsPartial = null;
    }

    /**
     * reset is the collWatchlists collection loaded partially
     *
     * @return void
     */
    public function resetPartialWatchlists($v = true)
    {
        $this->collWatchlistsPartial = $v;
    }

    /**
     * Initializes the collWatchlists collection.
     *
     * By default this just sets the collWatchlists collection to an empty array (like clearcollWatchlists());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initWatchlists($overrideExisting = true)
    {
        if (null !== $this->collWatchlists && !$overrideExisting) {
            return;
        }
        $this->collWatchlists = new PropelObjectCollection();
        $this->collWatchlists->setModel('Watchlist');
    }

    /**
     * Gets an array of Watchlist objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @return PropelObjectCollection|Watchlist[] List of Watchlist objects
     * @throws PropelException
     */
    public function getWatchlists($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collWatchlistsPartial && !$this->isNew();
        if (null === $this->collWatchlists || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collWatchlists) {
                // return empty collection
                $this->initWatchlists();
            } else {
                $collWatchlists = WatchlistQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collWatchlistsPartial && count($collWatchlists)) {
                      $this->initWatchlists(false);

                      foreach($collWatchlists as $obj) {
                        if (false == $this->collWatchlists->contains($obj)) {
                          $this->collWatchlists->append($obj);
                        }
                      }

                      $this->collWatchlistsPartial = true;
                    }

                    return $collWatchlists;
                }

                if($partial && $this->collWatchlists) {
                    foreach($this->collWatchlists as $obj) {
                        if($obj->isNew()) {
                            $collWatchlists[] = $obj;
                        }
                    }
                }

                $this->collWatchlists = $collWatchlists;
                $this->collWatchlistsPartial = false;
            }
        }

        return $this->collWatchlists;
    }

    /**
     * Sets a collection of Watchlist objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      PropelCollection $watchlists A Propel collection.
     * @param      PropelPDO $con Optional connection object
     */
    public function setWatchlists(PropelCollection $watchlists, PropelPDO $con = null)
    {
        $this->watchlistsScheduledForDeletion = $this->getWatchlists(new Criteria(), $con)->diff($watchlists);

        foreach ($this->watchlistsScheduledForDeletion as $watchlistRemoved) {
            $watchlistRemoved->setUser(null);
        }

        $this->collWatchlists = null;
        foreach ($watchlists as $watchlist) {
            $this->addWatchlist($watchlist);
        }

        $this->collWatchlists = $watchlists;
        $this->collWatchlistsPartial = false;
    }

    /**
     * Returns the number of related Watchlist objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      PropelPDO $con
     * @return int             Count of related Watchlist objects.
     * @throws PropelException
     */
    public function countWatchlists(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collWatchlistsPartial && !$this->isNew();
        if (null === $this->collWatchlists || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collWatchlists) {
                return 0;
            } else {
                if($partial && !$criteria) {
                    return count($this->getWatchlists());
                }
                $query = WatchlistQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByUser($this)
                    ->count($con);
            }
        } else {
            return count($this->collWatchlists);
        }
    }

    /**
     * Method called to associate a Watchlist object to this object
     * through the Watchlist foreign key attribute.
     *
     * @param    Watchlist $l Watchlist
     * @return   User The current object (for fluent API support)
     */
    public function addWatchlist(Watchlist $l)
    {
        if ($this->collWatchlists === null) {
            $this->initWatchlists();
            $this->collWatchlistsPartial = true;
        }
        if (!$this->collWatchlists->contains($l)) { // only add it if the **same** object is not already associated
            $this->doAddWatchlist($l);
        }

        return $this;
    }

    /**
     * @param	Watchlist $watchlist The watchlist object to add.
     */
    protected function doAddWatchlist($watchlist)
    {
        $this->collWatchlists[]= $watchlist;
        $watchlist->setUser($this);
    }

    /**
     * @param	Watchlist $watchlist The watchlist object to remove.
     */
    public function removeWatchlist($watchlist)
    {
        if ($this->getWatchlists()->contains($watchlist)) {
            $this->collWatchlists->remove($this->collWatchlists->search($watchlist));
            if (null === $this->watchlistsScheduledForDeletion) {
                $this->watchlistsScheduledForDeletion = clone $this->collWatchlists;
                $this->watchlistsScheduledForDeletion->clear();
            }
            $this->watchlistsScheduledForDeletion[]= $watchlist;
            $watchlist->setUser(null);
        }
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related Watchlists from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Watchlist[] List of Watchlist objects
     */
    public function getWatchlistsJoinItem($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = WatchlistQuery::create(null, $criteria);
        $query->joinWith('Item', $join_behavior);

        return $this->getWatchlists($query, $con);
    }

    /**
     * Clears out the collItems collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addItems()
     */
    public function clearItems()
    {
        $this->collItems = null; // important to set this to NULL since that means it is uninitialized
        $this->collItemsPartial = null;
    }

    /**
     * Initializes the collItems collection.
     *
     * By default this just sets the collItems collection to an empty collection (like clearItems());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initItems()
    {
        $this->collItems = new PropelObjectCollection();
        $this->collItems->setModel('Item');
    }

    /**
     * Gets a collection of Item objects related by a many-to-many relationship
     * to the current object by way of the watchlist cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|Item[] List of Item objects
     */
    public function getItems($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collItems || null !== $criteria) {
            if ($this->isNew() && null === $this->collItems) {
                // return empty collection
                $this->initItems();
            } else {
                $collItems = ItemQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collItems;
                }
                $this->collItems = $collItems;
            }
        }

        return $this->collItems;
    }

    /**
     * Sets a collection of Item objects related by a many-to-many relationship
     * to the current object by way of the watchlist cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      PropelCollection $items A Propel collection.
     * @param      PropelPDO $con Optional connection object
     */
    public function setItems(PropelCollection $items, PropelPDO $con = null)
    {
        $this->clearItems();
        $currentItems = $this->getItems();

        $this->itemsScheduledForDeletion = $currentItems->diff($items);

        foreach ($items as $item) {
            if (!$currentItems->contains($item)) {
                $this->doAddItem($item);
            }
        }

        $this->collItems = $items;
    }

    /**
     * Gets the number of Item objects related by a many-to-many relationship
     * to the current object by way of the watchlist cross-reference table.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      boolean $distinct Set to true to force count distinct
     * @param      PropelPDO $con Optional connection object
     *
     * @return int the number of related Item objects
     */
    public function countItems($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collItems || null !== $criteria) {
            if ($this->isNew() && null === $this->collItems) {
                return 0;
            } else {
                $query = ItemQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByUser($this)
                    ->count($con);
            }
        } else {
            return count($this->collItems);
        }
    }

    /**
     * Associate a Item object to this object
     * through the watchlist cross reference table.
     *
     * @param  Item $item The Watchlist object to relate
     * @return void
     */
    public function addItem(Item $item)
    {
        if ($this->collItems === null) {
            $this->initItems();
        }
        if (!$this->collItems->contains($item)) { // only add it if the **same** object is not already associated
            $this->doAddItem($item);

            $this->collItems[]= $item;
        }
    }

    /**
     * @param	Item $item The item object to add.
     */
    protected function doAddItem($item)
    {
        $watchlist = new Watchlist();
        $watchlist->setItem($item);
        $this->addWatchlist($watchlist);
    }

    /**
     * Remove a Item object to this object
     * through the watchlist cross reference table.
     *
     * @param      Item $item The Watchlist object to relate
     * @return void
     */
    public function removeItem(Item $item)
    {
        if ($this->getItems()->contains($item)) {
            $this->collItems->remove($this->collItems->search($item));
            if (null === $this->itemsScheduledForDeletion) {
                $this->itemsScheduledForDeletion = clone $this->collItems;
                $this->itemsScheduledForDeletion->clear();
            }
            $this->itemsScheduledForDeletion[]= $item;
        }
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->username = null;
        $this->email = null;
        $this->password = null;
        $this->roles = null;
        $this->hybrid_auth_provider_id = null;
        $this->hybrid_auth_id = null;
        $this->reset_password = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volumne/high-memory operations.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collWatchlists) {
                foreach ($this->collWatchlists as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collItems) {
                foreach ($this->collItems as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        if ($this->collWatchlists instanceof PropelCollection) {
            $this->collWatchlists->clearIterator();
        }
        $this->collWatchlists = null;
        if ($this->collItems instanceof PropelCollection) {
            $this->collItems->clearIterator();
        }
        $this->collItems = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(UserPeer::DEFAULT_STRING_FORMAT);
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

} // BaseUser
