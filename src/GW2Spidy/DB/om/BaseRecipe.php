<?php

namespace GW2Spidy\DB\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \DateTime;
use \DateTimeZone;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelDateTime;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use GW2Spidy\DB\Discipline;
use GW2Spidy\DB\DisciplineQuery;
use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\Recipe;
use GW2Spidy\DB\RecipeIngredient;
use GW2Spidy\DB\RecipeIngredientQuery;
use GW2Spidy\DB\RecipePeer;
use GW2Spidy\DB\RecipeQuery;

/**
 * Base class that represents a row from the 'recipe' table.
 *
 * 
 *
 * @package    propel.generator.gw2spidy.om
 */
abstract class BaseRecipe extends BaseObject implements Persistent
{

    /**
     * Peer class name
     */
    const PEER = 'GW2Spidy\\DB\\RecipePeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        RecipePeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinit loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the data_id field.
     * @var        int
     */
    protected $data_id;

    /**
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the discipline_id field.
     * @var        int
     */
    protected $discipline_id;

    /**
     * The value for the rating field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $rating;

    /**
     * The value for the result_item_id field.
     * @var        int
     */
    protected $result_item_id;

    /**
     * The value for the count field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $count;

    /**
     * The value for the cost field.
     * @var        int
     */
    protected $cost;

    /**
     * The value for the sell_price field.
     * @var        int
     */
    protected $sell_price;

    /**
     * The value for the profit field.
     * @var        int
     */
    protected $profit;

    /**
     * The value for the updated field.
     * @var        string
     */
    protected $updated;

    /**
     * The value for the requires_unlock field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $requires_unlock;

    /**
     * The value for the gw2db_id field.
     * @var        int
     */
    protected $gw2db_id;

    /**
     * The value for the gw2db_external_id field.
     * @var        int
     */
    protected $gw2db_external_id;

    /**
     * @var        Discipline
     */
    protected $aDiscipline;

    /**
     * @var        Item
     */
    protected $aResultItem;

    /**
     * @var        PropelObjectCollection|RecipeIngredient[] Collection to store aggregation of RecipeIngredient objects.
     */
    protected $collIngredients;
    protected $collIngredientsPartial;

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
    protected $ingredientsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->rating = 0;
        $this->count = 1;
        $this->requires_unlock = 0;
    }

    /**
     * Initializes internal state of BaseRecipe object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [data_id] column value.
     * 
     * @return   int
     */
    public function getDataId()
    {

        return $this->data_id;
    }

    /**
     * Get the [name] column value.
     * 
     * @return   string
     */
    public function getName()
    {

        return $this->name;
    }

    /**
     * Get the [discipline_id] column value.
     * 
     * @return   int
     */
    public function getDisciplineId()
    {

        return $this->discipline_id;
    }

    /**
     * Get the [rating] column value.
     * 
     * @return   int
     */
    public function getRating()
    {

        return $this->rating;
    }

    /**
     * Get the [result_item_id] column value.
     * 
     * @return   int
     */
    public function getResultItemId()
    {

        return $this->result_item_id;
    }

    /**
     * Get the [count] column value.
     * 
     * @return   int
     */
    public function getCount()
    {

        return $this->count;
    }

    /**
     * Get the [cost] column value.
     * 
     * @return   int
     */
    public function getCost()
    {

        return $this->cost;
    }

    /**
     * Get the [sell_price] column value.
     * 
     * @return   int
     */
    public function getSellPrice()
    {

        return $this->sell_price;
    }

    /**
     * Get the [profit] column value.
     * 
     * @return   int
     */
    public function getProfit()
    {

        return $this->profit;
    }

    /**
     * Get the [optionally formatted] temporal [updated] column value.
     * 
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *							If format is NULL, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdated($format = 'Y-m-d H:i:s')
    {
        if ($this->updated === null) {
            return null;
        }


        if ($this->updated === '0000-00-00 00:00:00') {
            // while technically this is not a default value of NULL,
            // this seems to be closest in meaning.
            return null;
        } else {
            try {
                $dt = new DateTime($this->updated);
            } catch (Exception $x) {
                throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->updated, true), $x);
            }
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is TRUE, we return a DateTime object.
            return $dt;
        } elseif (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        } else {
            return $dt->format($format);
        }
    }

    /**
     * Get the [requires_unlock] column value.
     * 
     * @return   int
     */
    public function getRequiresUnlock()
    {

        return $this->requires_unlock;
    }

    /**
     * Get the [gw2db_id] column value.
     * 
     * @return   int
     */
    public function getGw2dbId()
    {

        return $this->gw2db_id;
    }

    /**
     * Get the [gw2db_external_id] column value.
     * 
     * @return   int
     */
    public function getGw2dbExternalId()
    {

        return $this->gw2db_external_id;
    }

    /**
     * Set the value of [data_id] column.
     * 
     * @param      int $v new value
     * @return   Recipe The current object (for fluent API support)
     */
    public function setDataId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->data_id !== $v) {
            $this->data_id = $v;
            $this->modifiedColumns[] = RecipePeer::DATA_ID;
        }


        return $this;
    } // setDataId()

    /**
     * Set the value of [name] column.
     * 
     * @param      string $v new value
     * @return   Recipe The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = RecipePeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [discipline_id] column.
     * 
     * @param      int $v new value
     * @return   Recipe The current object (for fluent API support)
     */
    public function setDisciplineId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->discipline_id !== $v) {
            $this->discipline_id = $v;
            $this->modifiedColumns[] = RecipePeer::DISCIPLINE_ID;
        }

        if ($this->aDiscipline !== null && $this->aDiscipline->getId() !== $v) {
            $this->aDiscipline = null;
        }


        return $this;
    } // setDisciplineId()

    /**
     * Set the value of [rating] column.
     * 
     * @param      int $v new value
     * @return   Recipe The current object (for fluent API support)
     */
    public function setRating($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->rating !== $v) {
            $this->rating = $v;
            $this->modifiedColumns[] = RecipePeer::RATING;
        }


        return $this;
    } // setRating()

    /**
     * Set the value of [result_item_id] column.
     * 
     * @param      int $v new value
     * @return   Recipe The current object (for fluent API support)
     */
    public function setResultItemId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->result_item_id !== $v) {
            $this->result_item_id = $v;
            $this->modifiedColumns[] = RecipePeer::RESULT_ITEM_ID;
        }

        if ($this->aResultItem !== null && $this->aResultItem->getDataId() !== $v) {
            $this->aResultItem = null;
        }


        return $this;
    } // setResultItemId()

    /**
     * Set the value of [count] column.
     * 
     * @param      int $v new value
     * @return   Recipe The current object (for fluent API support)
     */
    public function setCount($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->count !== $v) {
            $this->count = $v;
            $this->modifiedColumns[] = RecipePeer::COUNT;
        }


        return $this;
    } // setCount()

    /**
     * Set the value of [cost] column.
     * 
     * @param      int $v new value
     * @return   Recipe The current object (for fluent API support)
     */
    public function setCost($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->cost !== $v) {
            $this->cost = $v;
            $this->modifiedColumns[] = RecipePeer::COST;
        }


        return $this;
    } // setCost()

    /**
     * Set the value of [sell_price] column.
     * 
     * @param      int $v new value
     * @return   Recipe The current object (for fluent API support)
     */
    public function setSellPrice($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->sell_price !== $v) {
            $this->sell_price = $v;
            $this->modifiedColumns[] = RecipePeer::SELL_PRICE;
        }


        return $this;
    } // setSellPrice()

    /**
     * Set the value of [profit] column.
     * 
     * @param      int $v new value
     * @return   Recipe The current object (for fluent API support)
     */
    public function setProfit($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->profit !== $v) {
            $this->profit = $v;
            $this->modifiedColumns[] = RecipePeer::PROFIT;
        }


        return $this;
    } // setProfit()

    /**
     * Sets the value of [updated] column to a normalized version of the date/time value specified.
     * 
     * @param      mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as NULL.
     * @return   Recipe The current object (for fluent API support)
     */
    public function setUpdated($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated !== null || $dt !== null) {
            $currentDateAsString = ($this->updated !== null && $tmpDt = new DateTime($this->updated)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updated = $newDateAsString;
                $this->modifiedColumns[] = RecipePeer::UPDATED;
            }
        } // if either are not null


        return $this;
    } // setUpdated()

    /**
     * Set the value of [requires_unlock] column.
     * 
     * @param      int $v new value
     * @return   Recipe The current object (for fluent API support)
     */
    public function setRequiresUnlock($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->requires_unlock !== $v) {
            $this->requires_unlock = $v;
            $this->modifiedColumns[] = RecipePeer::REQUIRES_UNLOCK;
        }


        return $this;
    } // setRequiresUnlock()

    /**
     * Set the value of [gw2db_id] column.
     * 
     * @param      int $v new value
     * @return   Recipe The current object (for fluent API support)
     */
    public function setGw2dbId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->gw2db_id !== $v) {
            $this->gw2db_id = $v;
            $this->modifiedColumns[] = RecipePeer::GW2DB_ID;
        }


        return $this;
    } // setGw2dbId()

    /**
     * Set the value of [gw2db_external_id] column.
     * 
     * @param      int $v new value
     * @return   Recipe The current object (for fluent API support)
     */
    public function setGw2dbExternalId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->gw2db_external_id !== $v) {
            $this->gw2db_external_id = $v;
            $this->modifiedColumns[] = RecipePeer::GW2DB_EXTERNAL_ID;
        }


        return $this;
    } // setGw2dbExternalId()

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
            if ($this->rating !== 0) {
                return false;
            }

            if ($this->count !== 1) {
                return false;
            }

            if ($this->requires_unlock !== 0) {
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

            $this->data_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->discipline_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
            $this->rating = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->result_item_id = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
            $this->count = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
            $this->cost = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
            $this->sell_price = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
            $this->profit = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
            $this->updated = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->requires_unlock = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
            $this->gw2db_id = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
            $this->gw2db_external_id = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 13; // 13 = RecipePeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Recipe object", $e);
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

        if ($this->aDiscipline !== null && $this->discipline_id !== $this->aDiscipline->getId()) {
            $this->aDiscipline = null;
        }
        if ($this->aResultItem !== null && $this->result_item_id !== $this->aResultItem->getDataId()) {
            $this->aResultItem = null;
        }
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
            $con = Propel::getConnection(RecipePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = RecipePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aDiscipline = null;
            $this->aResultItem = null;
            $this->collIngredients = null;

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
            $con = Propel::getConnection(RecipePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = RecipeQuery::create()
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
            $con = Propel::getConnection(RecipePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                RecipePeer::addInstanceToPool($this);
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

            // We call the save method on the following object(s) if they
            // were passed to this object by their coresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aDiscipline !== null) {
                if ($this->aDiscipline->isModified() || $this->aDiscipline->isNew()) {
                    $affectedRows += $this->aDiscipline->save($con);
                }
                $this->setDiscipline($this->aDiscipline);
            }

            if ($this->aResultItem !== null) {
                if ($this->aResultItem->isModified() || $this->aResultItem->isNew()) {
                    $affectedRows += $this->aResultItem->save($con);
                }
                $this->setResultItem($this->aResultItem);
            }

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
                    IngredientQuery::create()
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

            if ($this->ingredientsScheduledForDeletion !== null) {
                if (!$this->ingredientsScheduledForDeletion->isEmpty()) {
                    RecipeIngredientQuery::create()
                        ->filterByPrimaryKeys($this->ingredientsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ingredientsScheduledForDeletion = null;
                }
            }

            if ($this->collIngredients !== null) {
                foreach ($this->collIngredients as $referrerFK) {
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


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(RecipePeer::DATA_ID)) {
            $modifiedColumns[':p' . $index++]  = '`DATA_ID`';
        }
        if ($this->isColumnModified(RecipePeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = '`NAME`';
        }
        if ($this->isColumnModified(RecipePeer::DISCIPLINE_ID)) {
            $modifiedColumns[':p' . $index++]  = '`DISCIPLINE_ID`';
        }
        if ($this->isColumnModified(RecipePeer::RATING)) {
            $modifiedColumns[':p' . $index++]  = '`RATING`';
        }
        if ($this->isColumnModified(RecipePeer::RESULT_ITEM_ID)) {
            $modifiedColumns[':p' . $index++]  = '`RESULT_ITEM_ID`';
        }
        if ($this->isColumnModified(RecipePeer::COUNT)) {
            $modifiedColumns[':p' . $index++]  = '`COUNT`';
        }
        if ($this->isColumnModified(RecipePeer::COST)) {
            $modifiedColumns[':p' . $index++]  = '`COST`';
        }
        if ($this->isColumnModified(RecipePeer::SELL_PRICE)) {
            $modifiedColumns[':p' . $index++]  = '`SELL_PRICE`';
        }
        if ($this->isColumnModified(RecipePeer::PROFIT)) {
            $modifiedColumns[':p' . $index++]  = '`PROFIT`';
        }
        if ($this->isColumnModified(RecipePeer::UPDATED)) {
            $modifiedColumns[':p' . $index++]  = '`UPDATED`';
        }
        if ($this->isColumnModified(RecipePeer::REQUIRES_UNLOCK)) {
            $modifiedColumns[':p' . $index++]  = '`REQUIRES_UNLOCK`';
        }
        if ($this->isColumnModified(RecipePeer::GW2DB_ID)) {
            $modifiedColumns[':p' . $index++]  = '`GW2DB_ID`';
        }
        if ($this->isColumnModified(RecipePeer::GW2DB_EXTERNAL_ID)) {
            $modifiedColumns[':p' . $index++]  = '`GW2DB_EXTERNAL_ID`';
        }

        $sql = sprintf(
            'INSERT INTO `recipe` (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '`DATA_ID`':
						$stmt->bindValue($identifier, $this->data_id, PDO::PARAM_INT);
                        break;
                    case '`NAME`':
						$stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case '`DISCIPLINE_ID`':
						$stmt->bindValue($identifier, $this->discipline_id, PDO::PARAM_INT);
                        break;
                    case '`RATING`':
						$stmt->bindValue($identifier, $this->rating, PDO::PARAM_INT);
                        break;
                    case '`RESULT_ITEM_ID`':
						$stmt->bindValue($identifier, $this->result_item_id, PDO::PARAM_INT);
                        break;
                    case '`COUNT`':
						$stmt->bindValue($identifier, $this->count, PDO::PARAM_INT);
                        break;
                    case '`COST`':
						$stmt->bindValue($identifier, $this->cost, PDO::PARAM_INT);
                        break;
                    case '`SELL_PRICE`':
						$stmt->bindValue($identifier, $this->sell_price, PDO::PARAM_INT);
                        break;
                    case '`PROFIT`':
						$stmt->bindValue($identifier, $this->profit, PDO::PARAM_INT);
                        break;
                    case '`UPDATED`':
						$stmt->bindValue($identifier, $this->updated, PDO::PARAM_STR);
                        break;
                    case '`REQUIRES_UNLOCK`':
						$stmt->bindValue($identifier, $this->requires_unlock, PDO::PARAM_INT);
                        break;
                    case '`GW2DB_ID`':
						$stmt->bindValue($identifier, $this->gw2db_id, PDO::PARAM_INT);
                        break;
                    case '`GW2DB_EXTERNAL_ID`':
						$stmt->bindValue($identifier, $this->gw2db_external_id, PDO::PARAM_INT);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

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


            // We call the validate method on the following object(s) if they
            // were passed to this object by their coresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aDiscipline !== null) {
                if (!$this->aDiscipline->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aDiscipline->getValidationFailures());
                }
            }

            if ($this->aResultItem !== null) {
                if (!$this->aResultItem->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aResultItem->getValidationFailures());
                }
            }


            if (($retval = RecipePeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collIngredients !== null) {
                    foreach ($this->collIngredients as $referrerFK) {
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
        $pos = RecipePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDataId();
                break;
            case 1:
                return $this->getName();
                break;
            case 2:
                return $this->getDisciplineId();
                break;
            case 3:
                return $this->getRating();
                break;
            case 4:
                return $this->getResultItemId();
                break;
            case 5:
                return $this->getCount();
                break;
            case 6:
                return $this->getCost();
                break;
            case 7:
                return $this->getSellPrice();
                break;
            case 8:
                return $this->getProfit();
                break;
            case 9:
                return $this->getUpdated();
                break;
            case 10:
                return $this->getRequiresUnlock();
                break;
            case 11:
                return $this->getGw2dbId();
                break;
            case 12:
                return $this->getGw2dbExternalId();
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
        if (isset($alreadyDumpedObjects['Recipe'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Recipe'][$this->getPrimaryKey()] = true;
        $keys = RecipePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDataId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getDisciplineId(),
            $keys[3] => $this->getRating(),
            $keys[4] => $this->getResultItemId(),
            $keys[5] => $this->getCount(),
            $keys[6] => $this->getCost(),
            $keys[7] => $this->getSellPrice(),
            $keys[8] => $this->getProfit(),
            $keys[9] => $this->getUpdated(),
            $keys[10] => $this->getRequiresUnlock(),
            $keys[11] => $this->getGw2dbId(),
            $keys[12] => $this->getGw2dbExternalId(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->aDiscipline) {
                $result['Discipline'] = $this->aDiscipline->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aResultItem) {
                $result['ResultItem'] = $this->aResultItem->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collIngredients) {
                $result['Ingredients'] = $this->collIngredients->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = RecipePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setDataId($value);
                break;
            case 1:
                $this->setName($value);
                break;
            case 2:
                $this->setDisciplineId($value);
                break;
            case 3:
                $this->setRating($value);
                break;
            case 4:
                $this->setResultItemId($value);
                break;
            case 5:
                $this->setCount($value);
                break;
            case 6:
                $this->setCost($value);
                break;
            case 7:
                $this->setSellPrice($value);
                break;
            case 8:
                $this->setProfit($value);
                break;
            case 9:
                $this->setUpdated($value);
                break;
            case 10:
                $this->setRequiresUnlock($value);
                break;
            case 11:
                $this->setGw2dbId($value);
                break;
            case 12:
                $this->setGw2dbExternalId($value);
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
        $keys = RecipePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setDataId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDisciplineId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setRating($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setResultItemId($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setCount($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setCost($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setSellPrice($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setProfit($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setUpdated($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setRequiresUnlock($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setGw2dbId($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setGw2dbExternalId($arr[$keys[12]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(RecipePeer::DATABASE_NAME);

        if ($this->isColumnModified(RecipePeer::DATA_ID)) $criteria->add(RecipePeer::DATA_ID, $this->data_id);
        if ($this->isColumnModified(RecipePeer::NAME)) $criteria->add(RecipePeer::NAME, $this->name);
        if ($this->isColumnModified(RecipePeer::DISCIPLINE_ID)) $criteria->add(RecipePeer::DISCIPLINE_ID, $this->discipline_id);
        if ($this->isColumnModified(RecipePeer::RATING)) $criteria->add(RecipePeer::RATING, $this->rating);
        if ($this->isColumnModified(RecipePeer::RESULT_ITEM_ID)) $criteria->add(RecipePeer::RESULT_ITEM_ID, $this->result_item_id);
        if ($this->isColumnModified(RecipePeer::COUNT)) $criteria->add(RecipePeer::COUNT, $this->count);
        if ($this->isColumnModified(RecipePeer::COST)) $criteria->add(RecipePeer::COST, $this->cost);
        if ($this->isColumnModified(RecipePeer::SELL_PRICE)) $criteria->add(RecipePeer::SELL_PRICE, $this->sell_price);
        if ($this->isColumnModified(RecipePeer::PROFIT)) $criteria->add(RecipePeer::PROFIT, $this->profit);
        if ($this->isColumnModified(RecipePeer::UPDATED)) $criteria->add(RecipePeer::UPDATED, $this->updated);
        if ($this->isColumnModified(RecipePeer::REQUIRES_UNLOCK)) $criteria->add(RecipePeer::REQUIRES_UNLOCK, $this->requires_unlock);
        if ($this->isColumnModified(RecipePeer::GW2DB_ID)) $criteria->add(RecipePeer::GW2DB_ID, $this->gw2db_id);
        if ($this->isColumnModified(RecipePeer::GW2DB_EXTERNAL_ID)) $criteria->add(RecipePeer::GW2DB_EXTERNAL_ID, $this->gw2db_external_id);

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
        $criteria = new Criteria(RecipePeer::DATABASE_NAME);
        $criteria->add(RecipePeer::DATA_ID, $this->data_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return   int
     */
    public function getPrimaryKey()
    {
        return $this->getDataId();
    }

    /**
     * Generic method to set the primary key (data_id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setDataId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getDataId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of Recipe (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());
        $copyObj->setDisciplineId($this->getDisciplineId());
        $copyObj->setRating($this->getRating());
        $copyObj->setResultItemId($this->getResultItemId());
        $copyObj->setCount($this->getCount());
        $copyObj->setCost($this->getCost());
        $copyObj->setSellPrice($this->getSellPrice());
        $copyObj->setProfit($this->getProfit());
        $copyObj->setUpdated($this->getUpdated());
        $copyObj->setRequiresUnlock($this->getRequiresUnlock());
        $copyObj->setGw2dbId($this->getGw2dbId());
        $copyObj->setGw2dbExternalId($this->getGw2dbExternalId());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getIngredients() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addIngredient($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setDataId(NULL); // this is a auto-increment column, so set to default value
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
     * @return                 Recipe Clone of current object.
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
     * @return   RecipePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new RecipePeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Discipline object.
     *
     * @param                  Discipline $v
     * @return                 Recipe The current object (for fluent API support)
     * @throws PropelException
     */
    public function setDiscipline(Discipline $v = null)
    {
        if ($v === null) {
            $this->setDisciplineId(NULL);
        } else {
            $this->setDisciplineId($v->getId());
        }

        $this->aDiscipline = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Discipline object, it will not be re-added.
        if ($v !== null) {
            $v->addRecipe($this);
        }


        return $this;
    }


    /**
     * Get the associated Discipline object
     *
     * @param      PropelPDO $con Optional Connection object.
     * @return                 Discipline The associated Discipline object.
     * @throws PropelException
     */
    public function getDiscipline(PropelPDO $con = null)
    {
        if ($this->aDiscipline === null && ($this->discipline_id !== null)) {
            $this->aDiscipline = DisciplineQuery::create()->findPk($this->discipline_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aDiscipline->addRecipes($this);
             */
        }

        return $this->aDiscipline;
    }

    /**
     * Declares an association between this object and a Item object.
     *
     * @param                  Item $v
     * @return                 Recipe The current object (for fluent API support)
     * @throws PropelException
     */
    public function setResultItem(Item $v = null)
    {
        if ($v === null) {
            $this->setResultItemId(NULL);
        } else {
            $this->setResultItemId($v->getDataId());
        }

        $this->aResultItem = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Item object, it will not be re-added.
        if ($v !== null) {
            $v->addResultOfRecipe($this);
        }


        return $this;
    }


    /**
     * Get the associated Item object
     *
     * @param      PropelPDO $con Optional Connection object.
     * @return                 Item The associated Item object.
     * @throws PropelException
     */
    public function getResultItem(PropelPDO $con = null)
    {
        if ($this->aResultItem === null && ($this->result_item_id !== null)) {
            $this->aResultItem = ItemQuery::create()->findPk($this->result_item_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aResultItem->addResultOfRecipes($this);
             */
        }

        return $this->aResultItem;
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
        if ('Ingredient' == $relationName) {
            $this->initIngredients();
        }
    }

    /**
     * Clears out the collIngredients collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addIngredients()
     */
    public function clearIngredients()
    {
        $this->collIngredients = null; // important to set this to NULL since that means it is uninitialized
        $this->collIngredientsPartial = null;
    }

    /**
     * reset is the collIngredients collection loaded partially
     *
     * @return void
     */
    public function resetPartialIngredients($v = true)
    {
        $this->collIngredientsPartial = $v;
    }

    /**
     * Initializes the collIngredients collection.
     *
     * By default this just sets the collIngredients collection to an empty array (like clearcollIngredients());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initIngredients($overrideExisting = true)
    {
        if (null !== $this->collIngredients && !$overrideExisting) {
            return;
        }
        $this->collIngredients = new PropelObjectCollection();
        $this->collIngredients->setModel('RecipeIngredient');
    }

    /**
     * Gets an array of RecipeIngredient objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Recipe is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @return PropelObjectCollection|RecipeIngredient[] List of RecipeIngredient objects
     * @throws PropelException
     */
    public function getIngredients($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collIngredientsPartial && !$this->isNew();
        if (null === $this->collIngredients || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collIngredients) {
                // return empty collection
                $this->initIngredients();
            } else {
                $collIngredients = RecipeIngredientQuery::create(null, $criteria)
                    ->filterByRecipe($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collIngredientsPartial && count($collIngredients)) {
                      $this->initIngredients(false);

                      foreach($collIngredients as $obj) {
                        if (false == $this->collIngredients->contains($obj)) {
                          $this->collIngredients->append($obj);
                        }
                      }

                      $this->collIngredientsPartial = true;
                    }

                    return $collIngredients;
                }

                if($partial && $this->collIngredients) {
                    foreach($this->collIngredients as $obj) {
                        if($obj->isNew()) {
                            $collIngredients[] = $obj;
                        }
                    }
                }

                $this->collIngredients = $collIngredients;
                $this->collIngredientsPartial = false;
            }
        }

        return $this->collIngredients;
    }

    /**
     * Sets a collection of Ingredient objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      PropelCollection $ingredients A Propel collection.
     * @param      PropelPDO $con Optional connection object
     */
    public function setIngredients(PropelCollection $ingredients, PropelPDO $con = null)
    {
        $this->ingredientsScheduledForDeletion = $this->getIngredients(new Criteria(), $con)->diff($ingredients);

        foreach ($this->ingredientsScheduledForDeletion as $ingredientRemoved) {
            $ingredientRemoved->setRecipe(null);
        }

        $this->collIngredients = null;
        foreach ($ingredients as $ingredient) {
            $this->addIngredient($ingredient);
        }

        $this->collIngredients = $ingredients;
        $this->collIngredientsPartial = false;
    }

    /**
     * Returns the number of related RecipeIngredient objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      PropelPDO $con
     * @return int             Count of related RecipeIngredient objects.
     * @throws PropelException
     */
    public function countIngredients(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collIngredientsPartial && !$this->isNew();
        if (null === $this->collIngredients || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collIngredients) {
                return 0;
            } else {
                if($partial && !$criteria) {
                    return count($this->getIngredients());
                }
                $query = RecipeIngredientQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByRecipe($this)
                    ->count($con);
            }
        } else {
            return count($this->collIngredients);
        }
    }

    /**
     * Method called to associate a RecipeIngredient object to this object
     * through the RecipeIngredient foreign key attribute.
     *
     * @param    RecipeIngredient $l RecipeIngredient
     * @return   Recipe The current object (for fluent API support)
     */
    public function addIngredient(RecipeIngredient $l)
    {
        if ($this->collIngredients === null) {
            $this->initIngredients();
            $this->collIngredientsPartial = true;
        }
        if (!$this->collIngredients->contains($l)) { // only add it if the **same** object is not already associated
            $this->doAddIngredient($l);
        }

        return $this;
    }

    /**
     * @param	Ingredient $ingredient The ingredient object to add.
     */
    protected function doAddIngredient($ingredient)
    {
        $this->collIngredients[]= $ingredient;
        $ingredient->setRecipe($this);
    }

    /**
     * @param	Ingredient $ingredient The ingredient object to remove.
     */
    public function removeIngredient($ingredient)
    {
        if ($this->getIngredients()->contains($ingredient)) {
            $this->collIngredients->remove($this->collIngredients->search($ingredient));
            if (null === $this->ingredientsScheduledForDeletion) {
                $this->ingredientsScheduledForDeletion = clone $this->collIngredients;
                $this->ingredientsScheduledForDeletion->clear();
            }
            $this->ingredientsScheduledForDeletion[]= $ingredient;
            $ingredient->setRecipe(null);
        }
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Recipe is new, it will return
     * an empty collection; or if this Recipe has previously
     * been saved, it will retrieve related Ingredients from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Recipe.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|RecipeIngredient[] List of RecipeIngredient objects
     */
    public function getIngredientsJoinItem($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = RecipeIngredientQuery::create(null, $criteria);
        $query->joinWith('Item', $join_behavior);

        return $this->getIngredients($query, $con);
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
     * to the current object by way of the recipe_ingredient cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Recipe is new, it will return
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
                    ->filterByRecipe($this)
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
     * to the current object by way of the recipe_ingredient cross-reference table.
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
     * to the current object by way of the recipe_ingredient cross-reference table.
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
                    ->filterByRecipe($this)
                    ->count($con);
            }
        } else {
            return count($this->collItems);
        }
    }

    /**
     * Associate a Item object to this object
     * through the recipe_ingredient cross reference table.
     *
     * @param  Item $item The RecipeIngredient object to relate
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
        $recipeIngredient = new RecipeIngredient();
        $recipeIngredient->setItem($item);
        $this->addRecipeIngredient($recipeIngredient);
    }

    /**
     * Remove a Item object to this object
     * through the recipe_ingredient cross reference table.
     *
     * @param      Item $item The RecipeIngredient object to relate
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
        $this->data_id = null;
        $this->name = null;
        $this->discipline_id = null;
        $this->rating = null;
        $this->result_item_id = null;
        $this->count = null;
        $this->cost = null;
        $this->sell_price = null;
        $this->profit = null;
        $this->updated = null;
        $this->requires_unlock = null;
        $this->gw2db_id = null;
        $this->gw2db_external_id = null;
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
            if ($this->collIngredients) {
                foreach ($this->collIngredients as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collItems) {
                foreach ($this->collItems as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        if ($this->collIngredients instanceof PropelCollection) {
            $this->collIngredients->clearIterator();
        }
        $this->collIngredients = null;
        if ($this->collItems instanceof PropelCollection) {
            $this->collItems->clearIterator();
        }
        $this->collItems = null;
        $this->aDiscipline = null;
        $this->aResultItem = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(RecipePeer::DEFAULT_STRING_FORMAT);
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

} // BaseRecipe
