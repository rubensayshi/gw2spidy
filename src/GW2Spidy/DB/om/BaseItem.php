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
use GW2Spidy\DB\BuyListing;
use GW2Spidy\DB\BuyListingQuery;
use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemPeer;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\ItemSubType;
use GW2Spidy\DB\ItemSubTypeQuery;
use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\ItemTypeQuery;
use GW2Spidy\DB\Recipe;
use GW2Spidy\DB\RecipeIngredient;
use GW2Spidy\DB\RecipeIngredientQuery;
use GW2Spidy\DB\RecipeQuery;
use GW2Spidy\DB\SellListing;
use GW2Spidy\DB\SellListingQuery;
use GW2Spidy\DB\User;
use GW2Spidy\DB\UserQuery;
use GW2Spidy\DB\Watchlist;
use GW2Spidy\DB\WatchlistQuery;

/**
 * Base class that represents a row from the 'item' table.
 *
 * 
 *
 * @package    propel.generator.gw2spidy.om
 */
abstract class BaseItem extends BaseObject implements Persistent
{

    /**
     * Peer class name
     */
    const PEER = 'GW2Spidy\\DB\\ItemPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        ItemPeer
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
     * The value for the type_id field.
     * @var        int
     */
    protected $type_id;

    /**
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the gem_store_description field.
     * @var        string
     */
    protected $gem_store_description;

    /**
     * The value for the gem_store_blurb field.
     * @var        string
     */
    protected $gem_store_blurb;

    /**
     * The value for the restriction_level field.
     * @var        int
     */
    protected $restriction_level;

    /**
     * The value for the rarity field.
     * @var        int
     */
    protected $rarity;

    /**
     * The value for the vendor_sell_price field.
     * @var        int
     */
    protected $vendor_sell_price;

    /**
     * The value for the vendor_price field.
     * @var        int
     */
    protected $vendor_price;

    /**
     * The value for the karma_price field.
     * @var        int
     */
    protected $karma_price;

    /**
     * The value for the img field.
     * @var        string
     */
    protected $img;

    /**
     * The value for the rarity_word field.
     * @var        string
     */
    protected $rarity_word;

    /**
     * The value for the item_type_id field.
     * @var        int
     */
    protected $item_type_id;

    /**
     * The value for the item_sub_type_id field.
     * @var        int
     */
    protected $item_sub_type_id;

    /**
     * The value for the max_offer_unit_price field.
     * @var        int
     */
    protected $max_offer_unit_price;

    /**
     * The value for the min_sale_unit_price field.
     * @var        int
     */
    protected $min_sale_unit_price;

    /**
     * The value for the offer_availability field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $offer_availability;

    /**
     * The value for the sale_availability field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $sale_availability;

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
     * The value for the last_price_changed field.
     * @var        string
     */
    protected $last_price_changed;

    /**
     * The value for the sale_price_change_last_hour field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $sale_price_change_last_hour;

    /**
     * The value for the offer_price_change_last_hour field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $offer_price_change_last_hour;

    /**
     * @var        ItemType
     */
    protected $aItemType;

    /**
     * @var        ItemSubType
     */
    protected $aItemSubType;

    /**
     * @var        PropelObjectCollection|Recipe[] Collection to store aggregation of Recipe objects.
     */
    protected $collResultOfRecipes;
    protected $collResultOfRecipesPartial;

    /**
     * @var        PropelObjectCollection|RecipeIngredient[] Collection to store aggregation of RecipeIngredient objects.
     */
    protected $collIngredients;
    protected $collIngredientsPartial;

    /**
     * @var        PropelObjectCollection|SellListing[] Collection to store aggregation of SellListing objects.
     */
    protected $collSellListings;
    protected $collSellListingsPartial;

    /**
     * @var        PropelObjectCollection|BuyListing[] Collection to store aggregation of BuyListing objects.
     */
    protected $collBuyListings;
    protected $collBuyListingsPartial;

    /**
     * @var        PropelObjectCollection|Watchlist[] Collection to store aggregation of Watchlist objects.
     */
    protected $collOnWatchlists;
    protected $collOnWatchlistsPartial;

    /**
     * @var        PropelObjectCollection|Recipe[] Collection to store aggregation of Recipe objects.
     */
    protected $collRecipes;

    /**
     * @var        PropelObjectCollection|User[] Collection to store aggregation of User objects.
     */
    protected $collUsers;

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
    protected $recipesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $usersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $resultOfRecipesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ingredientsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $sellListingsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $buyListingsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $onWatchlistsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->offer_availability = 0;
        $this->sale_availability = 0;
        $this->sale_price_change_last_hour = 0;
        $this->offer_price_change_last_hour = 0;
    }

    /**
     * Initializes internal state of BaseItem object.
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
     * Get the [type_id] column value.
     * 
     * @return   int
     */
    public function getTypeId()
    {

        return $this->type_id;
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
     * Get the [gem_store_description] column value.
     * 
     * @return   string
     */
    public function getGemStoreDescription()
    {

        return $this->gem_store_description;
    }

    /**
     * Get the [gem_store_blurb] column value.
     * 
     * @return   string
     */
    public function getGemStoreBlurb()
    {

        return $this->gem_store_blurb;
    }

    /**
     * Get the [restriction_level] column value.
     * 
     * @return   int
     */
    public function getRestrictionLevel()
    {

        return $this->restriction_level;
    }

    /**
     * Get the [rarity] column value.
     * 
     * @return   int
     */
    public function getRarity()
    {

        return $this->rarity;
    }

    /**
     * Get the [vendor_sell_price] column value.
     * 
     * @return   int
     */
    public function getVendorSellPrice()
    {

        return $this->vendor_sell_price;
    }

    /**
     * Get the [vendor_price] column value.
     * 
     * @return   int
     */
    public function getVendorPrice()
    {

        return $this->vendor_price;
    }

    /**
     * Get the [karma_price] column value.
     * 
     * @return   int
     */
    public function getKarmaPrice()
    {

        return $this->karma_price;
    }

    /**
     * Get the [img] column value.
     * 
     * @return   string
     */
    public function getImg()
    {

        return $this->img;
    }

    /**
     * Get the [rarity_word] column value.
     * 
     * @return   string
     */
    public function getRarityWord()
    {

        return $this->rarity_word;
    }

    /**
     * Get the [item_type_id] column value.
     * 
     * @return   int
     */
    public function getItemTypeId()
    {

        return $this->item_type_id;
    }

    /**
     * Get the [item_sub_type_id] column value.
     * 
     * @return   int
     */
    public function getItemSubTypeId()
    {

        return $this->item_sub_type_id;
    }

    /**
     * Get the [max_offer_unit_price] column value.
     * 
     * @return   int
     */
    public function getMaxOfferUnitPrice()
    {

        return $this->max_offer_unit_price;
    }

    /**
     * Get the [min_sale_unit_price] column value.
     * 
     * @return   int
     */
    public function getMinSaleUnitPrice()
    {

        return $this->min_sale_unit_price;
    }

    /**
     * Get the [offer_availability] column value.
     * 
     * @return   int
     */
    public function getOfferAvailability()
    {

        return $this->offer_availability;
    }

    /**
     * Get the [sale_availability] column value.
     * 
     * @return   int
     */
    public function getSaleAvailability()
    {

        return $this->sale_availability;
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
     * Get the [optionally formatted] temporal [last_price_changed] column value.
     * 
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *							If format is NULL, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getLastPriceChanged($format = 'Y-m-d H:i:s')
    {
        if ($this->last_price_changed === null) {
            return null;
        }


        if ($this->last_price_changed === '0000-00-00 00:00:00') {
            // while technically this is not a default value of NULL,
            // this seems to be closest in meaning.
            return null;
        } else {
            try {
                $dt = new DateTime($this->last_price_changed);
            } catch (Exception $x) {
                throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->last_price_changed, true), $x);
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
     * Get the [sale_price_change_last_hour] column value.
     * 
     * @return   int
     */
    public function getSalePriceChangeLastHour()
    {

        return $this->sale_price_change_last_hour;
    }

    /**
     * Get the [offer_price_change_last_hour] column value.
     * 
     * @return   int
     */
    public function getOfferPriceChangeLastHour()
    {

        return $this->offer_price_change_last_hour;
    }

    /**
     * Set the value of [data_id] column.
     * 
     * @param      int $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setDataId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->data_id !== $v) {
            $this->data_id = $v;
            $this->modifiedColumns[] = ItemPeer::DATA_ID;
        }


        return $this;
    } // setDataId()

    /**
     * Set the value of [type_id] column.
     * 
     * @param      int $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setTypeId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->type_id !== $v) {
            $this->type_id = $v;
            $this->modifiedColumns[] = ItemPeer::TYPE_ID;
        }


        return $this;
    } // setTypeId()

    /**
     * Set the value of [name] column.
     * 
     * @param      string $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = ItemPeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [gem_store_description] column.
     * 
     * @param      string $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setGemStoreDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->gem_store_description !== $v) {
            $this->gem_store_description = $v;
            $this->modifiedColumns[] = ItemPeer::GEM_STORE_DESCRIPTION;
        }


        return $this;
    } // setGemStoreDescription()

    /**
     * Set the value of [gem_store_blurb] column.
     * 
     * @param      string $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setGemStoreBlurb($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->gem_store_blurb !== $v) {
            $this->gem_store_blurb = $v;
            $this->modifiedColumns[] = ItemPeer::GEM_STORE_BLURB;
        }


        return $this;
    } // setGemStoreBlurb()

    /**
     * Set the value of [restriction_level] column.
     * 
     * @param      int $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setRestrictionLevel($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->restriction_level !== $v) {
            $this->restriction_level = $v;
            $this->modifiedColumns[] = ItemPeer::RESTRICTION_LEVEL;
        }


        return $this;
    } // setRestrictionLevel()

    /**
     * Set the value of [rarity] column.
     * 
     * @param      int $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setRarity($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->rarity !== $v) {
            $this->rarity = $v;
            $this->modifiedColumns[] = ItemPeer::RARITY;
        }


        return $this;
    } // setRarity()

    /**
     * Set the value of [vendor_sell_price] column.
     * 
     * @param      int $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setVendorSellPrice($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->vendor_sell_price !== $v) {
            $this->vendor_sell_price = $v;
            $this->modifiedColumns[] = ItemPeer::VENDOR_SELL_PRICE;
        }


        return $this;
    } // setVendorSellPrice()

    /**
     * Set the value of [vendor_price] column.
     * 
     * @param      int $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setVendorPrice($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->vendor_price !== $v) {
            $this->vendor_price = $v;
            $this->modifiedColumns[] = ItemPeer::VENDOR_PRICE;
        }


        return $this;
    } // setVendorPrice()

    /**
     * Set the value of [karma_price] column.
     * 
     * @param      int $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setKarmaPrice($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->karma_price !== $v) {
            $this->karma_price = $v;
            $this->modifiedColumns[] = ItemPeer::KARMA_PRICE;
        }


        return $this;
    } // setKarmaPrice()

    /**
     * Set the value of [img] column.
     * 
     * @param      string $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setImg($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->img !== $v) {
            $this->img = $v;
            $this->modifiedColumns[] = ItemPeer::IMG;
        }


        return $this;
    } // setImg()

    /**
     * Set the value of [rarity_word] column.
     * 
     * @param      string $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setRarityWord($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->rarity_word !== $v) {
            $this->rarity_word = $v;
            $this->modifiedColumns[] = ItemPeer::RARITY_WORD;
        }


        return $this;
    } // setRarityWord()

    /**
     * Set the value of [item_type_id] column.
     * 
     * @param      int $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setItemTypeId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->item_type_id !== $v) {
            $this->item_type_id = $v;
            $this->modifiedColumns[] = ItemPeer::ITEM_TYPE_ID;
        }

        if ($this->aItemType !== null && $this->aItemType->getId() !== $v) {
            $this->aItemType = null;
        }


        return $this;
    } // setItemTypeId()

    /**
     * Set the value of [item_sub_type_id] column.
     * 
     * @param      int $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setItemSubTypeId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->item_sub_type_id !== $v) {
            $this->item_sub_type_id = $v;
            $this->modifiedColumns[] = ItemPeer::ITEM_SUB_TYPE_ID;
        }

        if ($this->aItemSubType !== null && $this->aItemSubType->getId() !== $v) {
            $this->aItemSubType = null;
        }


        return $this;
    } // setItemSubTypeId()

    /**
     * Set the value of [max_offer_unit_price] column.
     * 
     * @param      int $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setMaxOfferUnitPrice($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->max_offer_unit_price !== $v) {
            $this->max_offer_unit_price = $v;
            $this->modifiedColumns[] = ItemPeer::MAX_OFFER_UNIT_PRICE;
        }


        return $this;
    } // setMaxOfferUnitPrice()

    /**
     * Set the value of [min_sale_unit_price] column.
     * 
     * @param      int $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setMinSaleUnitPrice($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->min_sale_unit_price !== $v) {
            $this->min_sale_unit_price = $v;
            $this->modifiedColumns[] = ItemPeer::MIN_SALE_UNIT_PRICE;
        }


        return $this;
    } // setMinSaleUnitPrice()

    /**
     * Set the value of [offer_availability] column.
     * 
     * @param      int $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setOfferAvailability($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->offer_availability !== $v) {
            $this->offer_availability = $v;
            $this->modifiedColumns[] = ItemPeer::OFFER_AVAILABILITY;
        }


        return $this;
    } // setOfferAvailability()

    /**
     * Set the value of [sale_availability] column.
     * 
     * @param      int $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setSaleAvailability($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->sale_availability !== $v) {
            $this->sale_availability = $v;
            $this->modifiedColumns[] = ItemPeer::SALE_AVAILABILITY;
        }


        return $this;
    } // setSaleAvailability()

    /**
     * Set the value of [gw2db_id] column.
     * 
     * @param      int $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setGw2dbId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->gw2db_id !== $v) {
            $this->gw2db_id = $v;
            $this->modifiedColumns[] = ItemPeer::GW2DB_ID;
        }


        return $this;
    } // setGw2dbId()

    /**
     * Set the value of [gw2db_external_id] column.
     * 
     * @param      int $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setGw2dbExternalId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->gw2db_external_id !== $v) {
            $this->gw2db_external_id = $v;
            $this->modifiedColumns[] = ItemPeer::GW2DB_EXTERNAL_ID;
        }


        return $this;
    } // setGw2dbExternalId()

    /**
     * Sets the value of [last_price_changed] column to a normalized version of the date/time value specified.
     * 
     * @param      mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as NULL.
     * @return   Item The current object (for fluent API support)
     */
    public function setLastPriceChanged($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->last_price_changed !== null || $dt !== null) {
            $currentDateAsString = ($this->last_price_changed !== null && $tmpDt = new DateTime($this->last_price_changed)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->last_price_changed = $newDateAsString;
                $this->modifiedColumns[] = ItemPeer::LAST_PRICE_CHANGED;
            }
        } // if either are not null


        return $this;
    } // setLastPriceChanged()

    /**
     * Set the value of [sale_price_change_last_hour] column.
     * 
     * @param      int $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setSalePriceChangeLastHour($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->sale_price_change_last_hour !== $v) {
            $this->sale_price_change_last_hour = $v;
            $this->modifiedColumns[] = ItemPeer::SALE_PRICE_CHANGE_LAST_HOUR;
        }


        return $this;
    } // setSalePriceChangeLastHour()

    /**
     * Set the value of [offer_price_change_last_hour] column.
     * 
     * @param      int $v new value
     * @return   Item The current object (for fluent API support)
     */
    public function setOfferPriceChangeLastHour($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->offer_price_change_last_hour !== $v) {
            $this->offer_price_change_last_hour = $v;
            $this->modifiedColumns[] = ItemPeer::OFFER_PRICE_CHANGE_LAST_HOUR;
        }


        return $this;
    } // setOfferPriceChangeLastHour()

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
            if ($this->offer_availability !== 0) {
                return false;
            }

            if ($this->sale_availability !== 0) {
                return false;
            }

            if ($this->sale_price_change_last_hour !== 0) {
                return false;
            }

            if ($this->offer_price_change_last_hour !== 0) {
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
            $this->type_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->name = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->gem_store_description = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->gem_store_blurb = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->restriction_level = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
            $this->rarity = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
            $this->vendor_sell_price = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
            $this->vendor_price = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
            $this->karma_price = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
            $this->img = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->rarity_word = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
            $this->item_type_id = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
            $this->item_sub_type_id = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
            $this->max_offer_unit_price = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
            $this->min_sale_unit_price = ($row[$startcol + 15] !== null) ? (int) $row[$startcol + 15] : null;
            $this->offer_availability = ($row[$startcol + 16] !== null) ? (int) $row[$startcol + 16] : null;
            $this->sale_availability = ($row[$startcol + 17] !== null) ? (int) $row[$startcol + 17] : null;
            $this->gw2db_id = ($row[$startcol + 18] !== null) ? (int) $row[$startcol + 18] : null;
            $this->gw2db_external_id = ($row[$startcol + 19] !== null) ? (int) $row[$startcol + 19] : null;
            $this->last_price_changed = ($row[$startcol + 20] !== null) ? (string) $row[$startcol + 20] : null;
            $this->sale_price_change_last_hour = ($row[$startcol + 21] !== null) ? (int) $row[$startcol + 21] : null;
            $this->offer_price_change_last_hour = ($row[$startcol + 22] !== null) ? (int) $row[$startcol + 22] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 23; // 23 = ItemPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Item object", $e);
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

        if ($this->aItemType !== null && $this->item_type_id !== $this->aItemType->getId()) {
            $this->aItemType = null;
        }
        if ($this->aItemSubType !== null && $this->item_sub_type_id !== $this->aItemSubType->getId()) {
            $this->aItemSubType = null;
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
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = ItemPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aItemType = null;
            $this->aItemSubType = null;
            $this->collResultOfRecipes = null;

            $this->collIngredients = null;

            $this->collSellListings = null;

            $this->collBuyListings = null;

            $this->collOnWatchlists = null;

            $this->collRecipes = null;
            $this->collUsers = null;
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
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ItemQuery::create()
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
            $con = Propel::getConnection(ItemPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                ItemPeer::addInstanceToPool($this);
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

            if ($this->aItemType !== null) {
                if ($this->aItemType->isModified() || $this->aItemType->isNew()) {
                    $affectedRows += $this->aItemType->save($con);
                }
                $this->setItemType($this->aItemType);
            }

            if ($this->aItemSubType !== null) {
                if ($this->aItemSubType->isModified() || $this->aItemSubType->isNew()) {
                    $affectedRows += $this->aItemSubType->save($con);
                }
                $this->setItemSubType($this->aItemSubType);
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

            if ($this->recipesScheduledForDeletion !== null) {
                if (!$this->recipesScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->recipesScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($remotePk, $pk);
                    }
                    IngredientQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->recipesScheduledForDeletion = null;
                }

                foreach ($this->getRecipes() as $recipe) {
                    if ($recipe->isModified()) {
                        $recipe->save($con);
                    }
                }
            }

            if ($this->usersScheduledForDeletion !== null) {
                if (!$this->usersScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->usersScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($remotePk, $pk);
                    }
                    OnWatchlistQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->usersScheduledForDeletion = null;
                }

                foreach ($this->getUsers() as $user) {
                    if ($user->isModified()) {
                        $user->save($con);
                    }
                }
            }

            if ($this->resultOfRecipesScheduledForDeletion !== null) {
                if (!$this->resultOfRecipesScheduledForDeletion->isEmpty()) {
                    foreach ($this->resultOfRecipesScheduledForDeletion as $resultOfRecipe) {
                        // need to save related object because we set the relation to null
                        $resultOfRecipe->save($con);
                    }
                    $this->resultOfRecipesScheduledForDeletion = null;
                }
            }

            if ($this->collResultOfRecipes !== null) {
                foreach ($this->collResultOfRecipes as $referrerFK) {
                    if (!$referrerFK->isDeleted()) {
                        $affectedRows += $referrerFK->save($con);
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

            if ($this->sellListingsScheduledForDeletion !== null) {
                if (!$this->sellListingsScheduledForDeletion->isEmpty()) {
                    SellListingQuery::create()
                        ->filterByPrimaryKeys($this->sellListingsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->sellListingsScheduledForDeletion = null;
                }
            }

            if ($this->collSellListings !== null) {
                foreach ($this->collSellListings as $referrerFK) {
                    if (!$referrerFK->isDeleted()) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->buyListingsScheduledForDeletion !== null) {
                if (!$this->buyListingsScheduledForDeletion->isEmpty()) {
                    BuyListingQuery::create()
                        ->filterByPrimaryKeys($this->buyListingsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->buyListingsScheduledForDeletion = null;
                }
            }

            if ($this->collBuyListings !== null) {
                foreach ($this->collBuyListings as $referrerFK) {
                    if (!$referrerFK->isDeleted()) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->onWatchlistsScheduledForDeletion !== null) {
                if (!$this->onWatchlistsScheduledForDeletion->isEmpty()) {
                    WatchlistQuery::create()
                        ->filterByPrimaryKeys($this->onWatchlistsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->onWatchlistsScheduledForDeletion = null;
                }
            }

            if ($this->collOnWatchlists !== null) {
                foreach ($this->collOnWatchlists as $referrerFK) {
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
        if ($this->isColumnModified(ItemPeer::DATA_ID)) {
            $modifiedColumns[':p' . $index++]  = '`DATA_ID`';
        }
        if ($this->isColumnModified(ItemPeer::TYPE_ID)) {
            $modifiedColumns[':p' . $index++]  = '`TYPE_ID`';
        }
        if ($this->isColumnModified(ItemPeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = '`NAME`';
        }
        if ($this->isColumnModified(ItemPeer::GEM_STORE_DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '`GEM_STORE_DESCRIPTION`';
        }
        if ($this->isColumnModified(ItemPeer::GEM_STORE_BLURB)) {
            $modifiedColumns[':p' . $index++]  = '`GEM_STORE_BLURB`';
        }
        if ($this->isColumnModified(ItemPeer::RESTRICTION_LEVEL)) {
            $modifiedColumns[':p' . $index++]  = '`RESTRICTION_LEVEL`';
        }
        if ($this->isColumnModified(ItemPeer::RARITY)) {
            $modifiedColumns[':p' . $index++]  = '`RARITY`';
        }
        if ($this->isColumnModified(ItemPeer::VENDOR_SELL_PRICE)) {
            $modifiedColumns[':p' . $index++]  = '`VENDOR_SELL_PRICE`';
        }
        if ($this->isColumnModified(ItemPeer::VENDOR_PRICE)) {
            $modifiedColumns[':p' . $index++]  = '`VENDOR_PRICE`';
        }
        if ($this->isColumnModified(ItemPeer::KARMA_PRICE)) {
            $modifiedColumns[':p' . $index++]  = '`KARMA_PRICE`';
        }
        if ($this->isColumnModified(ItemPeer::IMG)) {
            $modifiedColumns[':p' . $index++]  = '`IMG`';
        }
        if ($this->isColumnModified(ItemPeer::RARITY_WORD)) {
            $modifiedColumns[':p' . $index++]  = '`RARITY_WORD`';
        }
        if ($this->isColumnModified(ItemPeer::ITEM_TYPE_ID)) {
            $modifiedColumns[':p' . $index++]  = '`ITEM_TYPE_ID`';
        }
        if ($this->isColumnModified(ItemPeer::ITEM_SUB_TYPE_ID)) {
            $modifiedColumns[':p' . $index++]  = '`ITEM_SUB_TYPE_ID`';
        }
        if ($this->isColumnModified(ItemPeer::MAX_OFFER_UNIT_PRICE)) {
            $modifiedColumns[':p' . $index++]  = '`MAX_OFFER_UNIT_PRICE`';
        }
        if ($this->isColumnModified(ItemPeer::MIN_SALE_UNIT_PRICE)) {
            $modifiedColumns[':p' . $index++]  = '`MIN_SALE_UNIT_PRICE`';
        }
        if ($this->isColumnModified(ItemPeer::OFFER_AVAILABILITY)) {
            $modifiedColumns[':p' . $index++]  = '`OFFER_AVAILABILITY`';
        }
        if ($this->isColumnModified(ItemPeer::SALE_AVAILABILITY)) {
            $modifiedColumns[':p' . $index++]  = '`SALE_AVAILABILITY`';
        }
        if ($this->isColumnModified(ItemPeer::GW2DB_ID)) {
            $modifiedColumns[':p' . $index++]  = '`GW2DB_ID`';
        }
        if ($this->isColumnModified(ItemPeer::GW2DB_EXTERNAL_ID)) {
            $modifiedColumns[':p' . $index++]  = '`GW2DB_EXTERNAL_ID`';
        }
        if ($this->isColumnModified(ItemPeer::LAST_PRICE_CHANGED)) {
            $modifiedColumns[':p' . $index++]  = '`LAST_PRICE_CHANGED`';
        }
        if ($this->isColumnModified(ItemPeer::SALE_PRICE_CHANGE_LAST_HOUR)) {
            $modifiedColumns[':p' . $index++]  = '`SALE_PRICE_CHANGE_LAST_HOUR`';
        }
        if ($this->isColumnModified(ItemPeer::OFFER_PRICE_CHANGE_LAST_HOUR)) {
            $modifiedColumns[':p' . $index++]  = '`OFFER_PRICE_CHANGE_LAST_HOUR`';
        }

        $sql = sprintf(
            'INSERT INTO `item` (%s) VALUES (%s)',
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
                    case '`TYPE_ID`':
						$stmt->bindValue($identifier, $this->type_id, PDO::PARAM_INT);
                        break;
                    case '`NAME`':
						$stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case '`GEM_STORE_DESCRIPTION`':
						$stmt->bindValue($identifier, $this->gem_store_description, PDO::PARAM_STR);
                        break;
                    case '`GEM_STORE_BLURB`':
						$stmt->bindValue($identifier, $this->gem_store_blurb, PDO::PARAM_STR);
                        break;
                    case '`RESTRICTION_LEVEL`':
						$stmt->bindValue($identifier, $this->restriction_level, PDO::PARAM_INT);
                        break;
                    case '`RARITY`':
						$stmt->bindValue($identifier, $this->rarity, PDO::PARAM_INT);
                        break;
                    case '`VENDOR_SELL_PRICE`':
						$stmt->bindValue($identifier, $this->vendor_sell_price, PDO::PARAM_INT);
                        break;
                    case '`VENDOR_PRICE`':
						$stmt->bindValue($identifier, $this->vendor_price, PDO::PARAM_INT);
                        break;
                    case '`KARMA_PRICE`':
						$stmt->bindValue($identifier, $this->karma_price, PDO::PARAM_INT);
                        break;
                    case '`IMG`':
						$stmt->bindValue($identifier, $this->img, PDO::PARAM_STR);
                        break;
                    case '`RARITY_WORD`':
						$stmt->bindValue($identifier, $this->rarity_word, PDO::PARAM_STR);
                        break;
                    case '`ITEM_TYPE_ID`':
						$stmt->bindValue($identifier, $this->item_type_id, PDO::PARAM_INT);
                        break;
                    case '`ITEM_SUB_TYPE_ID`':
						$stmt->bindValue($identifier, $this->item_sub_type_id, PDO::PARAM_INT);
                        break;
                    case '`MAX_OFFER_UNIT_PRICE`':
						$stmt->bindValue($identifier, $this->max_offer_unit_price, PDO::PARAM_INT);
                        break;
                    case '`MIN_SALE_UNIT_PRICE`':
						$stmt->bindValue($identifier, $this->min_sale_unit_price, PDO::PARAM_INT);
                        break;
                    case '`OFFER_AVAILABILITY`':
						$stmt->bindValue($identifier, $this->offer_availability, PDO::PARAM_INT);
                        break;
                    case '`SALE_AVAILABILITY`':
						$stmt->bindValue($identifier, $this->sale_availability, PDO::PARAM_INT);
                        break;
                    case '`GW2DB_ID`':
						$stmt->bindValue($identifier, $this->gw2db_id, PDO::PARAM_INT);
                        break;
                    case '`GW2DB_EXTERNAL_ID`':
						$stmt->bindValue($identifier, $this->gw2db_external_id, PDO::PARAM_INT);
                        break;
                    case '`LAST_PRICE_CHANGED`':
						$stmt->bindValue($identifier, $this->last_price_changed, PDO::PARAM_STR);
                        break;
                    case '`SALE_PRICE_CHANGE_LAST_HOUR`':
						$stmt->bindValue($identifier, $this->sale_price_change_last_hour, PDO::PARAM_INT);
                        break;
                    case '`OFFER_PRICE_CHANGE_LAST_HOUR`':
						$stmt->bindValue($identifier, $this->offer_price_change_last_hour, PDO::PARAM_INT);
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

            if ($this->aItemType !== null) {
                if (!$this->aItemType->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aItemType->getValidationFailures());
                }
            }

            if ($this->aItemSubType !== null) {
                if (!$this->aItemSubType->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aItemSubType->getValidationFailures());
                }
            }


            if (($retval = ItemPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collResultOfRecipes !== null) {
                    foreach ($this->collResultOfRecipes as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collIngredients !== null) {
                    foreach ($this->collIngredients as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collSellListings !== null) {
                    foreach ($this->collSellListings as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collBuyListings !== null) {
                    foreach ($this->collBuyListings as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collOnWatchlists !== null) {
                    foreach ($this->collOnWatchlists as $referrerFK) {
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
        $pos = ItemPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getTypeId();
                break;
            case 2:
                return $this->getName();
                break;
            case 3:
                return $this->getGemStoreDescription();
                break;
            case 4:
                return $this->getGemStoreBlurb();
                break;
            case 5:
                return $this->getRestrictionLevel();
                break;
            case 6:
                return $this->getRarity();
                break;
            case 7:
                return $this->getVendorSellPrice();
                break;
            case 8:
                return $this->getVendorPrice();
                break;
            case 9:
                return $this->getKarmaPrice();
                break;
            case 10:
                return $this->getImg();
                break;
            case 11:
                return $this->getRarityWord();
                break;
            case 12:
                return $this->getItemTypeId();
                break;
            case 13:
                return $this->getItemSubTypeId();
                break;
            case 14:
                return $this->getMaxOfferUnitPrice();
                break;
            case 15:
                return $this->getMinSaleUnitPrice();
                break;
            case 16:
                return $this->getOfferAvailability();
                break;
            case 17:
                return $this->getSaleAvailability();
                break;
            case 18:
                return $this->getGw2dbId();
                break;
            case 19:
                return $this->getGw2dbExternalId();
                break;
            case 20:
                return $this->getLastPriceChanged();
                break;
            case 21:
                return $this->getSalePriceChangeLastHour();
                break;
            case 22:
                return $this->getOfferPriceChangeLastHour();
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
        if (isset($alreadyDumpedObjects['Item'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Item'][$this->getPrimaryKey()] = true;
        $keys = ItemPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDataId(),
            $keys[1] => $this->getTypeId(),
            $keys[2] => $this->getName(),
            $keys[3] => $this->getGemStoreDescription(),
            $keys[4] => $this->getGemStoreBlurb(),
            $keys[5] => $this->getRestrictionLevel(),
            $keys[6] => $this->getRarity(),
            $keys[7] => $this->getVendorSellPrice(),
            $keys[8] => $this->getVendorPrice(),
            $keys[9] => $this->getKarmaPrice(),
            $keys[10] => $this->getImg(),
            $keys[11] => $this->getRarityWord(),
            $keys[12] => $this->getItemTypeId(),
            $keys[13] => $this->getItemSubTypeId(),
            $keys[14] => $this->getMaxOfferUnitPrice(),
            $keys[15] => $this->getMinSaleUnitPrice(),
            $keys[16] => $this->getOfferAvailability(),
            $keys[17] => $this->getSaleAvailability(),
            $keys[18] => $this->getGw2dbId(),
            $keys[19] => $this->getGw2dbExternalId(),
            $keys[20] => $this->getLastPriceChanged(),
            $keys[21] => $this->getSalePriceChangeLastHour(),
            $keys[22] => $this->getOfferPriceChangeLastHour(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->aItemType) {
                $result['ItemType'] = $this->aItemType->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aItemSubType) {
                $result['ItemSubType'] = $this->aItemSubType->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collResultOfRecipes) {
                $result['ResultOfRecipes'] = $this->collResultOfRecipes->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collIngredients) {
                $result['Ingredients'] = $this->collIngredients->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collSellListings) {
                $result['SellListings'] = $this->collSellListings->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collBuyListings) {
                $result['BuyListings'] = $this->collBuyListings->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collOnWatchlists) {
                $result['OnWatchlists'] = $this->collOnWatchlists->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = ItemPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setTypeId($value);
                break;
            case 2:
                $this->setName($value);
                break;
            case 3:
                $this->setGemStoreDescription($value);
                break;
            case 4:
                $this->setGemStoreBlurb($value);
                break;
            case 5:
                $this->setRestrictionLevel($value);
                break;
            case 6:
                $this->setRarity($value);
                break;
            case 7:
                $this->setVendorSellPrice($value);
                break;
            case 8:
                $this->setVendorPrice($value);
                break;
            case 9:
                $this->setKarmaPrice($value);
                break;
            case 10:
                $this->setImg($value);
                break;
            case 11:
                $this->setRarityWord($value);
                break;
            case 12:
                $this->setItemTypeId($value);
                break;
            case 13:
                $this->setItemSubTypeId($value);
                break;
            case 14:
                $this->setMaxOfferUnitPrice($value);
                break;
            case 15:
                $this->setMinSaleUnitPrice($value);
                break;
            case 16:
                $this->setOfferAvailability($value);
                break;
            case 17:
                $this->setSaleAvailability($value);
                break;
            case 18:
                $this->setGw2dbId($value);
                break;
            case 19:
                $this->setGw2dbExternalId($value);
                break;
            case 20:
                $this->setLastPriceChanged($value);
                break;
            case 21:
                $this->setSalePriceChangeLastHour($value);
                break;
            case 22:
                $this->setOfferPriceChangeLastHour($value);
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
        $keys = ItemPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setDataId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setTypeId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setName($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setGemStoreDescription($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setGemStoreBlurb($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setRestrictionLevel($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setRarity($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setVendorSellPrice($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setVendorPrice($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setKarmaPrice($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setImg($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setRarityWord($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setItemTypeId($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setItemSubTypeId($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setMaxOfferUnitPrice($arr[$keys[14]]);
        if (array_key_exists($keys[15], $arr)) $this->setMinSaleUnitPrice($arr[$keys[15]]);
        if (array_key_exists($keys[16], $arr)) $this->setOfferAvailability($arr[$keys[16]]);
        if (array_key_exists($keys[17], $arr)) $this->setSaleAvailability($arr[$keys[17]]);
        if (array_key_exists($keys[18], $arr)) $this->setGw2dbId($arr[$keys[18]]);
        if (array_key_exists($keys[19], $arr)) $this->setGw2dbExternalId($arr[$keys[19]]);
        if (array_key_exists($keys[20], $arr)) $this->setLastPriceChanged($arr[$keys[20]]);
        if (array_key_exists($keys[21], $arr)) $this->setSalePriceChangeLastHour($arr[$keys[21]]);
        if (array_key_exists($keys[22], $arr)) $this->setOfferPriceChangeLastHour($arr[$keys[22]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ItemPeer::DATABASE_NAME);

        if ($this->isColumnModified(ItemPeer::DATA_ID)) $criteria->add(ItemPeer::DATA_ID, $this->data_id);
        if ($this->isColumnModified(ItemPeer::TYPE_ID)) $criteria->add(ItemPeer::TYPE_ID, $this->type_id);
        if ($this->isColumnModified(ItemPeer::NAME)) $criteria->add(ItemPeer::NAME, $this->name);
        if ($this->isColumnModified(ItemPeer::GEM_STORE_DESCRIPTION)) $criteria->add(ItemPeer::GEM_STORE_DESCRIPTION, $this->gem_store_description);
        if ($this->isColumnModified(ItemPeer::GEM_STORE_BLURB)) $criteria->add(ItemPeer::GEM_STORE_BLURB, $this->gem_store_blurb);
        if ($this->isColumnModified(ItemPeer::RESTRICTION_LEVEL)) $criteria->add(ItemPeer::RESTRICTION_LEVEL, $this->restriction_level);
        if ($this->isColumnModified(ItemPeer::RARITY)) $criteria->add(ItemPeer::RARITY, $this->rarity);
        if ($this->isColumnModified(ItemPeer::VENDOR_SELL_PRICE)) $criteria->add(ItemPeer::VENDOR_SELL_PRICE, $this->vendor_sell_price);
        if ($this->isColumnModified(ItemPeer::VENDOR_PRICE)) $criteria->add(ItemPeer::VENDOR_PRICE, $this->vendor_price);
        if ($this->isColumnModified(ItemPeer::KARMA_PRICE)) $criteria->add(ItemPeer::KARMA_PRICE, $this->karma_price);
        if ($this->isColumnModified(ItemPeer::IMG)) $criteria->add(ItemPeer::IMG, $this->img);
        if ($this->isColumnModified(ItemPeer::RARITY_WORD)) $criteria->add(ItemPeer::RARITY_WORD, $this->rarity_word);
        if ($this->isColumnModified(ItemPeer::ITEM_TYPE_ID)) $criteria->add(ItemPeer::ITEM_TYPE_ID, $this->item_type_id);
        if ($this->isColumnModified(ItemPeer::ITEM_SUB_TYPE_ID)) $criteria->add(ItemPeer::ITEM_SUB_TYPE_ID, $this->item_sub_type_id);
        if ($this->isColumnModified(ItemPeer::MAX_OFFER_UNIT_PRICE)) $criteria->add(ItemPeer::MAX_OFFER_UNIT_PRICE, $this->max_offer_unit_price);
        if ($this->isColumnModified(ItemPeer::MIN_SALE_UNIT_PRICE)) $criteria->add(ItemPeer::MIN_SALE_UNIT_PRICE, $this->min_sale_unit_price);
        if ($this->isColumnModified(ItemPeer::OFFER_AVAILABILITY)) $criteria->add(ItemPeer::OFFER_AVAILABILITY, $this->offer_availability);
        if ($this->isColumnModified(ItemPeer::SALE_AVAILABILITY)) $criteria->add(ItemPeer::SALE_AVAILABILITY, $this->sale_availability);
        if ($this->isColumnModified(ItemPeer::GW2DB_ID)) $criteria->add(ItemPeer::GW2DB_ID, $this->gw2db_id);
        if ($this->isColumnModified(ItemPeer::GW2DB_EXTERNAL_ID)) $criteria->add(ItemPeer::GW2DB_EXTERNAL_ID, $this->gw2db_external_id);
        if ($this->isColumnModified(ItemPeer::LAST_PRICE_CHANGED)) $criteria->add(ItemPeer::LAST_PRICE_CHANGED, $this->last_price_changed);
        if ($this->isColumnModified(ItemPeer::SALE_PRICE_CHANGE_LAST_HOUR)) $criteria->add(ItemPeer::SALE_PRICE_CHANGE_LAST_HOUR, $this->sale_price_change_last_hour);
        if ($this->isColumnModified(ItemPeer::OFFER_PRICE_CHANGE_LAST_HOUR)) $criteria->add(ItemPeer::OFFER_PRICE_CHANGE_LAST_HOUR, $this->offer_price_change_last_hour);

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
        $criteria = new Criteria(ItemPeer::DATABASE_NAME);
        $criteria->add(ItemPeer::DATA_ID, $this->data_id);

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
     * @param      object $copyObj An object of Item (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setTypeId($this->getTypeId());
        $copyObj->setName($this->getName());
        $copyObj->setGemStoreDescription($this->getGemStoreDescription());
        $copyObj->setGemStoreBlurb($this->getGemStoreBlurb());
        $copyObj->setRestrictionLevel($this->getRestrictionLevel());
        $copyObj->setRarity($this->getRarity());
        $copyObj->setVendorSellPrice($this->getVendorSellPrice());
        $copyObj->setVendorPrice($this->getVendorPrice());
        $copyObj->setKarmaPrice($this->getKarmaPrice());
        $copyObj->setImg($this->getImg());
        $copyObj->setRarityWord($this->getRarityWord());
        $copyObj->setItemTypeId($this->getItemTypeId());
        $copyObj->setItemSubTypeId($this->getItemSubTypeId());
        $copyObj->setMaxOfferUnitPrice($this->getMaxOfferUnitPrice());
        $copyObj->setMinSaleUnitPrice($this->getMinSaleUnitPrice());
        $copyObj->setOfferAvailability($this->getOfferAvailability());
        $copyObj->setSaleAvailability($this->getSaleAvailability());
        $copyObj->setGw2dbId($this->getGw2dbId());
        $copyObj->setGw2dbExternalId($this->getGw2dbExternalId());
        $copyObj->setLastPriceChanged($this->getLastPriceChanged());
        $copyObj->setSalePriceChangeLastHour($this->getSalePriceChangeLastHour());
        $copyObj->setOfferPriceChangeLastHour($this->getOfferPriceChangeLastHour());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getResultOfRecipes() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addResultOfRecipe($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getIngredients() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addIngredient($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getSellListings() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addSellListing($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getBuyListings() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addBuyListing($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getOnWatchlists() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addOnWatchlist($relObj->copy($deepCopy));
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
     * @return                 Item Clone of current object.
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
     * @return   ItemPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new ItemPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a ItemType object.
     *
     * @param                  ItemType $v
     * @return                 Item The current object (for fluent API support)
     * @throws PropelException
     */
    public function setItemType(ItemType $v = null)
    {
        if ($v === null) {
            $this->setItemTypeId(NULL);
        } else {
            $this->setItemTypeId($v->getId());
        }

        $this->aItemType = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ItemType object, it will not be re-added.
        if ($v !== null) {
            $v->addItem($this);
        }


        return $this;
    }


    /**
     * Get the associated ItemType object
     *
     * @param      PropelPDO $con Optional Connection object.
     * @return                 ItemType The associated ItemType object.
     * @throws PropelException
     */
    public function getItemType(PropelPDO $con = null)
    {
        if ($this->aItemType === null && ($this->item_type_id !== null)) {
            $this->aItemType = ItemTypeQuery::create()->findPk($this->item_type_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aItemType->addItems($this);
             */
        }

        return $this->aItemType;
    }

    /**
     * Declares an association between this object and a ItemSubType object.
     *
     * @param                  ItemSubType $v
     * @return                 Item The current object (for fluent API support)
     * @throws PropelException
     */
    public function setItemSubType(ItemSubType $v = null)
    {
        if ($v === null) {
            $this->setItemSubTypeId(NULL);
        } else {
            $this->setItemSubTypeId($v->getId());
        }

        $this->aItemSubType = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ItemSubType object, it will not be re-added.
        if ($v !== null) {
            $v->addItem($this);
        }


        return $this;
    }


    /**
     * Get the associated ItemSubType object
     *
     * @param      PropelPDO $con Optional Connection object.
     * @return                 ItemSubType The associated ItemSubType object.
     * @throws PropelException
     */
    public function getItemSubType(PropelPDO $con = null)
    {
        if ($this->aItemSubType === null && ($this->item_sub_type_id !== null)) {
            $this->aItemSubType = ItemSubTypeQuery::create()
                ->filterByItem($this) // here
                ->findOne($con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aItemSubType->addItems($this);
             */
        }

        return $this->aItemSubType;
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
        if ('ResultOfRecipe' == $relationName) {
            $this->initResultOfRecipes();
        }
        if ('Ingredient' == $relationName) {
            $this->initIngredients();
        }
        if ('SellListing' == $relationName) {
            $this->initSellListings();
        }
        if ('BuyListing' == $relationName) {
            $this->initBuyListings();
        }
        if ('OnWatchlist' == $relationName) {
            $this->initOnWatchlists();
        }
    }

    /**
     * Clears out the collResultOfRecipes collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addResultOfRecipes()
     */
    public function clearResultOfRecipes()
    {
        $this->collResultOfRecipes = null; // important to set this to NULL since that means it is uninitialized
        $this->collResultOfRecipesPartial = null;
    }

    /**
     * reset is the collResultOfRecipes collection loaded partially
     *
     * @return void
     */
    public function resetPartialResultOfRecipes($v = true)
    {
        $this->collResultOfRecipesPartial = $v;
    }

    /**
     * Initializes the collResultOfRecipes collection.
     *
     * By default this just sets the collResultOfRecipes collection to an empty array (like clearcollResultOfRecipes());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initResultOfRecipes($overrideExisting = true)
    {
        if (null !== $this->collResultOfRecipes && !$overrideExisting) {
            return;
        }
        $this->collResultOfRecipes = new PropelObjectCollection();
        $this->collResultOfRecipes->setModel('Recipe');
    }

    /**
     * Gets an array of Recipe objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Item is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @return PropelObjectCollection|Recipe[] List of Recipe objects
     * @throws PropelException
     */
    public function getResultOfRecipes($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collResultOfRecipesPartial && !$this->isNew();
        if (null === $this->collResultOfRecipes || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collResultOfRecipes) {
                // return empty collection
                $this->initResultOfRecipes();
            } else {
                $collResultOfRecipes = RecipeQuery::create(null, $criteria)
                    ->filterByResultItem($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collResultOfRecipesPartial && count($collResultOfRecipes)) {
                      $this->initResultOfRecipes(false);

                      foreach($collResultOfRecipes as $obj) {
                        if (false == $this->collResultOfRecipes->contains($obj)) {
                          $this->collResultOfRecipes->append($obj);
                        }
                      }

                      $this->collResultOfRecipesPartial = true;
                    }

                    return $collResultOfRecipes;
                }

                if($partial && $this->collResultOfRecipes) {
                    foreach($this->collResultOfRecipes as $obj) {
                        if($obj->isNew()) {
                            $collResultOfRecipes[] = $obj;
                        }
                    }
                }

                $this->collResultOfRecipes = $collResultOfRecipes;
                $this->collResultOfRecipesPartial = false;
            }
        }

        return $this->collResultOfRecipes;
    }

    /**
     * Sets a collection of ResultOfRecipe objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      PropelCollection $resultOfRecipes A Propel collection.
     * @param      PropelPDO $con Optional connection object
     */
    public function setResultOfRecipes(PropelCollection $resultOfRecipes, PropelPDO $con = null)
    {
        $this->resultOfRecipesScheduledForDeletion = $this->getResultOfRecipes(new Criteria(), $con)->diff($resultOfRecipes);

        foreach ($this->resultOfRecipesScheduledForDeletion as $resultOfRecipeRemoved) {
            $resultOfRecipeRemoved->setResultItem(null);
        }

        $this->collResultOfRecipes = null;
        foreach ($resultOfRecipes as $resultOfRecipe) {
            $this->addResultOfRecipe($resultOfRecipe);
        }

        $this->collResultOfRecipes = $resultOfRecipes;
        $this->collResultOfRecipesPartial = false;
    }

    /**
     * Returns the number of related Recipe objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      PropelPDO $con
     * @return int             Count of related Recipe objects.
     * @throws PropelException
     */
    public function countResultOfRecipes(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collResultOfRecipesPartial && !$this->isNew();
        if (null === $this->collResultOfRecipes || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collResultOfRecipes) {
                return 0;
            } else {
                if($partial && !$criteria) {
                    return count($this->getResultOfRecipes());
                }
                $query = RecipeQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByResultItem($this)
                    ->count($con);
            }
        } else {
            return count($this->collResultOfRecipes);
        }
    }

    /**
     * Method called to associate a Recipe object to this object
     * through the Recipe foreign key attribute.
     *
     * @param    Recipe $l Recipe
     * @return   Item The current object (for fluent API support)
     */
    public function addResultOfRecipe(Recipe $l)
    {
        if ($this->collResultOfRecipes === null) {
            $this->initResultOfRecipes();
            $this->collResultOfRecipesPartial = true;
        }
        if (!$this->collResultOfRecipes->contains($l)) { // only add it if the **same** object is not already associated
            $this->doAddResultOfRecipe($l);
        }

        return $this;
    }

    /**
     * @param	ResultOfRecipe $resultOfRecipe The resultOfRecipe object to add.
     */
    protected function doAddResultOfRecipe($resultOfRecipe)
    {
        $this->collResultOfRecipes[]= $resultOfRecipe;
        $resultOfRecipe->setResultItem($this);
    }

    /**
     * @param	ResultOfRecipe $resultOfRecipe The resultOfRecipe object to remove.
     */
    public function removeResultOfRecipe($resultOfRecipe)
    {
        if ($this->getResultOfRecipes()->contains($resultOfRecipe)) {
            $this->collResultOfRecipes->remove($this->collResultOfRecipes->search($resultOfRecipe));
            if (null === $this->resultOfRecipesScheduledForDeletion) {
                $this->resultOfRecipesScheduledForDeletion = clone $this->collResultOfRecipes;
                $this->resultOfRecipesScheduledForDeletion->clear();
            }
            $this->resultOfRecipesScheduledForDeletion[]= $resultOfRecipe;
            $resultOfRecipe->setResultItem(null);
        }
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Item is new, it will return
     * an empty collection; or if this Item has previously
     * been saved, it will retrieve related ResultOfRecipes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Item.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Recipe[] List of Recipe objects
     */
    public function getResultOfRecipesJoinDiscipline($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = RecipeQuery::create(null, $criteria);
        $query->joinWith('Discipline', $join_behavior);

        return $this->getResultOfRecipes($query, $con);
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
     * If this Item is new, it will return
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
                    ->filterByItem($this)
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
            $ingredientRemoved->setItem(null);
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
                    ->filterByItem($this)
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
     * @return   Item The current object (for fluent API support)
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
        $ingredient->setItem($this);
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
            $ingredient->setItem(null);
        }
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Item is new, it will return
     * an empty collection; or if this Item has previously
     * been saved, it will retrieve related Ingredients from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Item.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|RecipeIngredient[] List of RecipeIngredient objects
     */
    public function getIngredientsJoinRecipe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = RecipeIngredientQuery::create(null, $criteria);
        $query->joinWith('Recipe', $join_behavior);

        return $this->getIngredients($query, $con);
    }

    /**
     * Clears out the collSellListings collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addSellListings()
     */
    public function clearSellListings()
    {
        $this->collSellListings = null; // important to set this to NULL since that means it is uninitialized
        $this->collSellListingsPartial = null;
    }

    /**
     * reset is the collSellListings collection loaded partially
     *
     * @return void
     */
    public function resetPartialSellListings($v = true)
    {
        $this->collSellListingsPartial = $v;
    }

    /**
     * Initializes the collSellListings collection.
     *
     * By default this just sets the collSellListings collection to an empty array (like clearcollSellListings());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initSellListings($overrideExisting = true)
    {
        if (null !== $this->collSellListings && !$overrideExisting) {
            return;
        }
        $this->collSellListings = new PropelObjectCollection();
        $this->collSellListings->setModel('SellListing');
    }

    /**
     * Gets an array of SellListing objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Item is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @return PropelObjectCollection|SellListing[] List of SellListing objects
     * @throws PropelException
     */
    public function getSellListings($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collSellListingsPartial && !$this->isNew();
        if (null === $this->collSellListings || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collSellListings) {
                // return empty collection
                $this->initSellListings();
            } else {
                $collSellListings = SellListingQuery::create(null, $criteria)
                    ->filterByItem($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collSellListingsPartial && count($collSellListings)) {
                      $this->initSellListings(false);

                      foreach($collSellListings as $obj) {
                        if (false == $this->collSellListings->contains($obj)) {
                          $this->collSellListings->append($obj);
                        }
                      }

                      $this->collSellListingsPartial = true;
                    }

                    return $collSellListings;
                }

                if($partial && $this->collSellListings) {
                    foreach($this->collSellListings as $obj) {
                        if($obj->isNew()) {
                            $collSellListings[] = $obj;
                        }
                    }
                }

                $this->collSellListings = $collSellListings;
                $this->collSellListingsPartial = false;
            }
        }

        return $this->collSellListings;
    }

    /**
     * Sets a collection of SellListing objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      PropelCollection $sellListings A Propel collection.
     * @param      PropelPDO $con Optional connection object
     */
    public function setSellListings(PropelCollection $sellListings, PropelPDO $con = null)
    {
        $this->sellListingsScheduledForDeletion = $this->getSellListings(new Criteria(), $con)->diff($sellListings);

        foreach ($this->sellListingsScheduledForDeletion as $sellListingRemoved) {
            $sellListingRemoved->setItem(null);
        }

        $this->collSellListings = null;
        foreach ($sellListings as $sellListing) {
            $this->addSellListing($sellListing);
        }

        $this->collSellListings = $sellListings;
        $this->collSellListingsPartial = false;
    }

    /**
     * Returns the number of related SellListing objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      PropelPDO $con
     * @return int             Count of related SellListing objects.
     * @throws PropelException
     */
    public function countSellListings(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collSellListingsPartial && !$this->isNew();
        if (null === $this->collSellListings || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collSellListings) {
                return 0;
            } else {
                if($partial && !$criteria) {
                    return count($this->getSellListings());
                }
                $query = SellListingQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByItem($this)
                    ->count($con);
            }
        } else {
            return count($this->collSellListings);
        }
    }

    /**
     * Method called to associate a SellListing object to this object
     * through the SellListing foreign key attribute.
     *
     * @param    SellListing $l SellListing
     * @return   Item The current object (for fluent API support)
     */
    public function addSellListing(SellListing $l)
    {
        if ($this->collSellListings === null) {
            $this->initSellListings();
            $this->collSellListingsPartial = true;
        }
        if (!$this->collSellListings->contains($l)) { // only add it if the **same** object is not already associated
            $this->doAddSellListing($l);
        }

        return $this;
    }

    /**
     * @param	SellListing $sellListing The sellListing object to add.
     */
    protected function doAddSellListing($sellListing)
    {
        $this->collSellListings[]= $sellListing;
        $sellListing->setItem($this);
    }

    /**
     * @param	SellListing $sellListing The sellListing object to remove.
     */
    public function removeSellListing($sellListing)
    {
        if ($this->getSellListings()->contains($sellListing)) {
            $this->collSellListings->remove($this->collSellListings->search($sellListing));
            if (null === $this->sellListingsScheduledForDeletion) {
                $this->sellListingsScheduledForDeletion = clone $this->collSellListings;
                $this->sellListingsScheduledForDeletion->clear();
            }
            $this->sellListingsScheduledForDeletion[]= $sellListing;
            $sellListing->setItem(null);
        }
    }

    /**
     * Clears out the collBuyListings collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addBuyListings()
     */
    public function clearBuyListings()
    {
        $this->collBuyListings = null; // important to set this to NULL since that means it is uninitialized
        $this->collBuyListingsPartial = null;
    }

    /**
     * reset is the collBuyListings collection loaded partially
     *
     * @return void
     */
    public function resetPartialBuyListings($v = true)
    {
        $this->collBuyListingsPartial = $v;
    }

    /**
     * Initializes the collBuyListings collection.
     *
     * By default this just sets the collBuyListings collection to an empty array (like clearcollBuyListings());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initBuyListings($overrideExisting = true)
    {
        if (null !== $this->collBuyListings && !$overrideExisting) {
            return;
        }
        $this->collBuyListings = new PropelObjectCollection();
        $this->collBuyListings->setModel('BuyListing');
    }

    /**
     * Gets an array of BuyListing objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Item is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @return PropelObjectCollection|BuyListing[] List of BuyListing objects
     * @throws PropelException
     */
    public function getBuyListings($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collBuyListingsPartial && !$this->isNew();
        if (null === $this->collBuyListings || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collBuyListings) {
                // return empty collection
                $this->initBuyListings();
            } else {
                $collBuyListings = BuyListingQuery::create(null, $criteria)
                    ->filterByItem($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collBuyListingsPartial && count($collBuyListings)) {
                      $this->initBuyListings(false);

                      foreach($collBuyListings as $obj) {
                        if (false == $this->collBuyListings->contains($obj)) {
                          $this->collBuyListings->append($obj);
                        }
                      }

                      $this->collBuyListingsPartial = true;
                    }

                    return $collBuyListings;
                }

                if($partial && $this->collBuyListings) {
                    foreach($this->collBuyListings as $obj) {
                        if($obj->isNew()) {
                            $collBuyListings[] = $obj;
                        }
                    }
                }

                $this->collBuyListings = $collBuyListings;
                $this->collBuyListingsPartial = false;
            }
        }

        return $this->collBuyListings;
    }

    /**
     * Sets a collection of BuyListing objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      PropelCollection $buyListings A Propel collection.
     * @param      PropelPDO $con Optional connection object
     */
    public function setBuyListings(PropelCollection $buyListings, PropelPDO $con = null)
    {
        $this->buyListingsScheduledForDeletion = $this->getBuyListings(new Criteria(), $con)->diff($buyListings);

        foreach ($this->buyListingsScheduledForDeletion as $buyListingRemoved) {
            $buyListingRemoved->setItem(null);
        }

        $this->collBuyListings = null;
        foreach ($buyListings as $buyListing) {
            $this->addBuyListing($buyListing);
        }

        $this->collBuyListings = $buyListings;
        $this->collBuyListingsPartial = false;
    }

    /**
     * Returns the number of related BuyListing objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      PropelPDO $con
     * @return int             Count of related BuyListing objects.
     * @throws PropelException
     */
    public function countBuyListings(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collBuyListingsPartial && !$this->isNew();
        if (null === $this->collBuyListings || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collBuyListings) {
                return 0;
            } else {
                if($partial && !$criteria) {
                    return count($this->getBuyListings());
                }
                $query = BuyListingQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByItem($this)
                    ->count($con);
            }
        } else {
            return count($this->collBuyListings);
        }
    }

    /**
     * Method called to associate a BuyListing object to this object
     * through the BuyListing foreign key attribute.
     *
     * @param    BuyListing $l BuyListing
     * @return   Item The current object (for fluent API support)
     */
    public function addBuyListing(BuyListing $l)
    {
        if ($this->collBuyListings === null) {
            $this->initBuyListings();
            $this->collBuyListingsPartial = true;
        }
        if (!$this->collBuyListings->contains($l)) { // only add it if the **same** object is not already associated
            $this->doAddBuyListing($l);
        }

        return $this;
    }

    /**
     * @param	BuyListing $buyListing The buyListing object to add.
     */
    protected function doAddBuyListing($buyListing)
    {
        $this->collBuyListings[]= $buyListing;
        $buyListing->setItem($this);
    }

    /**
     * @param	BuyListing $buyListing The buyListing object to remove.
     */
    public function removeBuyListing($buyListing)
    {
        if ($this->getBuyListings()->contains($buyListing)) {
            $this->collBuyListings->remove($this->collBuyListings->search($buyListing));
            if (null === $this->buyListingsScheduledForDeletion) {
                $this->buyListingsScheduledForDeletion = clone $this->collBuyListings;
                $this->buyListingsScheduledForDeletion->clear();
            }
            $this->buyListingsScheduledForDeletion[]= $buyListing;
            $buyListing->setItem(null);
        }
    }

    /**
     * Clears out the collOnWatchlists collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addOnWatchlists()
     */
    public function clearOnWatchlists()
    {
        $this->collOnWatchlists = null; // important to set this to NULL since that means it is uninitialized
        $this->collOnWatchlistsPartial = null;
    }

    /**
     * reset is the collOnWatchlists collection loaded partially
     *
     * @return void
     */
    public function resetPartialOnWatchlists($v = true)
    {
        $this->collOnWatchlistsPartial = $v;
    }

    /**
     * Initializes the collOnWatchlists collection.
     *
     * By default this just sets the collOnWatchlists collection to an empty array (like clearcollOnWatchlists());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initOnWatchlists($overrideExisting = true)
    {
        if (null !== $this->collOnWatchlists && !$overrideExisting) {
            return;
        }
        $this->collOnWatchlists = new PropelObjectCollection();
        $this->collOnWatchlists->setModel('Watchlist');
    }

    /**
     * Gets an array of Watchlist objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Item is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @return PropelObjectCollection|Watchlist[] List of Watchlist objects
     * @throws PropelException
     */
    public function getOnWatchlists($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collOnWatchlistsPartial && !$this->isNew();
        if (null === $this->collOnWatchlists || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collOnWatchlists) {
                // return empty collection
                $this->initOnWatchlists();
            } else {
                $collOnWatchlists = WatchlistQuery::create(null, $criteria)
                    ->filterByItem($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collOnWatchlistsPartial && count($collOnWatchlists)) {
                      $this->initOnWatchlists(false);

                      foreach($collOnWatchlists as $obj) {
                        if (false == $this->collOnWatchlists->contains($obj)) {
                          $this->collOnWatchlists->append($obj);
                        }
                      }

                      $this->collOnWatchlistsPartial = true;
                    }

                    return $collOnWatchlists;
                }

                if($partial && $this->collOnWatchlists) {
                    foreach($this->collOnWatchlists as $obj) {
                        if($obj->isNew()) {
                            $collOnWatchlists[] = $obj;
                        }
                    }
                }

                $this->collOnWatchlists = $collOnWatchlists;
                $this->collOnWatchlistsPartial = false;
            }
        }

        return $this->collOnWatchlists;
    }

    /**
     * Sets a collection of OnWatchlist objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      PropelCollection $onWatchlists A Propel collection.
     * @param      PropelPDO $con Optional connection object
     */
    public function setOnWatchlists(PropelCollection $onWatchlists, PropelPDO $con = null)
    {
        $this->onWatchlistsScheduledForDeletion = $this->getOnWatchlists(new Criteria(), $con)->diff($onWatchlists);

        foreach ($this->onWatchlistsScheduledForDeletion as $onWatchlistRemoved) {
            $onWatchlistRemoved->setItem(null);
        }

        $this->collOnWatchlists = null;
        foreach ($onWatchlists as $onWatchlist) {
            $this->addOnWatchlist($onWatchlist);
        }

        $this->collOnWatchlists = $onWatchlists;
        $this->collOnWatchlistsPartial = false;
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
    public function countOnWatchlists(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collOnWatchlistsPartial && !$this->isNew();
        if (null === $this->collOnWatchlists || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collOnWatchlists) {
                return 0;
            } else {
                if($partial && !$criteria) {
                    return count($this->getOnWatchlists());
                }
                $query = WatchlistQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByItem($this)
                    ->count($con);
            }
        } else {
            return count($this->collOnWatchlists);
        }
    }

    /**
     * Method called to associate a Watchlist object to this object
     * through the Watchlist foreign key attribute.
     *
     * @param    Watchlist $l Watchlist
     * @return   Item The current object (for fluent API support)
     */
    public function addOnWatchlist(Watchlist $l)
    {
        if ($this->collOnWatchlists === null) {
            $this->initOnWatchlists();
            $this->collOnWatchlistsPartial = true;
        }
        if (!$this->collOnWatchlists->contains($l)) { // only add it if the **same** object is not already associated
            $this->doAddOnWatchlist($l);
        }

        return $this;
    }

    /**
     * @param	OnWatchlist $onWatchlist The onWatchlist object to add.
     */
    protected function doAddOnWatchlist($onWatchlist)
    {
        $this->collOnWatchlists[]= $onWatchlist;
        $onWatchlist->setItem($this);
    }

    /**
     * @param	OnWatchlist $onWatchlist The onWatchlist object to remove.
     */
    public function removeOnWatchlist($onWatchlist)
    {
        if ($this->getOnWatchlists()->contains($onWatchlist)) {
            $this->collOnWatchlists->remove($this->collOnWatchlists->search($onWatchlist));
            if (null === $this->onWatchlistsScheduledForDeletion) {
                $this->onWatchlistsScheduledForDeletion = clone $this->collOnWatchlists;
                $this->onWatchlistsScheduledForDeletion->clear();
            }
            $this->onWatchlistsScheduledForDeletion[]= $onWatchlist;
            $onWatchlist->setItem(null);
        }
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Item is new, it will return
     * an empty collection; or if this Item has previously
     * been saved, it will retrieve related OnWatchlists from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Item.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Watchlist[] List of Watchlist objects
     */
    public function getOnWatchlistsJoinUser($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = WatchlistQuery::create(null, $criteria);
        $query->joinWith('User', $join_behavior);

        return $this->getOnWatchlists($query, $con);
    }

    /**
     * Clears out the collRecipes collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addRecipes()
     */
    public function clearRecipes()
    {
        $this->collRecipes = null; // important to set this to NULL since that means it is uninitialized
        $this->collRecipesPartial = null;
    }

    /**
     * Initializes the collRecipes collection.
     *
     * By default this just sets the collRecipes collection to an empty collection (like clearRecipes());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initRecipes()
    {
        $this->collRecipes = new PropelObjectCollection();
        $this->collRecipes->setModel('Recipe');
    }

    /**
     * Gets a collection of Recipe objects related by a many-to-many relationship
     * to the current object by way of the recipe_ingredient cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Item is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|Recipe[] List of Recipe objects
     */
    public function getRecipes($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collRecipes || null !== $criteria) {
            if ($this->isNew() && null === $this->collRecipes) {
                // return empty collection
                $this->initRecipes();
            } else {
                $collRecipes = RecipeQuery::create(null, $criteria)
                    ->filterByItem($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collRecipes;
                }
                $this->collRecipes = $collRecipes;
            }
        }

        return $this->collRecipes;
    }

    /**
     * Sets a collection of Recipe objects related by a many-to-many relationship
     * to the current object by way of the recipe_ingredient cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      PropelCollection $recipes A Propel collection.
     * @param      PropelPDO $con Optional connection object
     */
    public function setRecipes(PropelCollection $recipes, PropelPDO $con = null)
    {
        $this->clearRecipes();
        $currentRecipes = $this->getRecipes();

        $this->recipesScheduledForDeletion = $currentRecipes->diff($recipes);

        foreach ($recipes as $recipe) {
            if (!$currentRecipes->contains($recipe)) {
                $this->doAddRecipe($recipe);
            }
        }

        $this->collRecipes = $recipes;
    }

    /**
     * Gets the number of Recipe objects related by a many-to-many relationship
     * to the current object by way of the recipe_ingredient cross-reference table.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      boolean $distinct Set to true to force count distinct
     * @param      PropelPDO $con Optional connection object
     *
     * @return int the number of related Recipe objects
     */
    public function countRecipes($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collRecipes || null !== $criteria) {
            if ($this->isNew() && null === $this->collRecipes) {
                return 0;
            } else {
                $query = RecipeQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByItem($this)
                    ->count($con);
            }
        } else {
            return count($this->collRecipes);
        }
    }

    /**
     * Associate a Recipe object to this object
     * through the recipe_ingredient cross reference table.
     *
     * @param  Recipe $recipe The RecipeIngredient object to relate
     * @return void
     */
    public function addRecipe(Recipe $recipe)
    {
        if ($this->collRecipes === null) {
            $this->initRecipes();
        }
        if (!$this->collRecipes->contains($recipe)) { // only add it if the **same** object is not already associated
            $this->doAddRecipe($recipe);

            $this->collRecipes[]= $recipe;
        }
    }

    /**
     * @param	Recipe $recipe The recipe object to add.
     */
    protected function doAddRecipe($recipe)
    {
        $recipeIngredient = new RecipeIngredient();
        $recipeIngredient->setRecipe($recipe);
        $this->addRecipeIngredient($recipeIngredient);
    }

    /**
     * Remove a Recipe object to this object
     * through the recipe_ingredient cross reference table.
     *
     * @param      Recipe $recipe The RecipeIngredient object to relate
     * @return void
     */
    public function removeRecipe(Recipe $recipe)
    {
        if ($this->getRecipes()->contains($recipe)) {
            $this->collRecipes->remove($this->collRecipes->search($recipe));
            if (null === $this->recipesScheduledForDeletion) {
                $this->recipesScheduledForDeletion = clone $this->collRecipes;
                $this->recipesScheduledForDeletion->clear();
            }
            $this->recipesScheduledForDeletion[]= $recipe;
        }
    }

    /**
     * Clears out the collUsers collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addUsers()
     */
    public function clearUsers()
    {
        $this->collUsers = null; // important to set this to NULL since that means it is uninitialized
        $this->collUsersPartial = null;
    }

    /**
     * Initializes the collUsers collection.
     *
     * By default this just sets the collUsers collection to an empty collection (like clearUsers());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initUsers()
    {
        $this->collUsers = new PropelObjectCollection();
        $this->collUsers->setModel('User');
    }

    /**
     * Gets a collection of User objects related by a many-to-many relationship
     * to the current object by way of the watchlist cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Item is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|User[] List of User objects
     */
    public function getUsers($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collUsers || null !== $criteria) {
            if ($this->isNew() && null === $this->collUsers) {
                // return empty collection
                $this->initUsers();
            } else {
                $collUsers = UserQuery::create(null, $criteria)
                    ->filterByItem($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collUsers;
                }
                $this->collUsers = $collUsers;
            }
        }

        return $this->collUsers;
    }

    /**
     * Sets a collection of User objects related by a many-to-many relationship
     * to the current object by way of the watchlist cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      PropelCollection $users A Propel collection.
     * @param      PropelPDO $con Optional connection object
     */
    public function setUsers(PropelCollection $users, PropelPDO $con = null)
    {
        $this->clearUsers();
        $currentUsers = $this->getUsers();

        $this->usersScheduledForDeletion = $currentUsers->diff($users);

        foreach ($users as $user) {
            if (!$currentUsers->contains($user)) {
                $this->doAddUser($user);
            }
        }

        $this->collUsers = $users;
    }

    /**
     * Gets the number of User objects related by a many-to-many relationship
     * to the current object by way of the watchlist cross-reference table.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      boolean $distinct Set to true to force count distinct
     * @param      PropelPDO $con Optional connection object
     *
     * @return int the number of related User objects
     */
    public function countUsers($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collUsers || null !== $criteria) {
            if ($this->isNew() && null === $this->collUsers) {
                return 0;
            } else {
                $query = UserQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByItem($this)
                    ->count($con);
            }
        } else {
            return count($this->collUsers);
        }
    }

    /**
     * Associate a User object to this object
     * through the watchlist cross reference table.
     *
     * @param  User $user The Watchlist object to relate
     * @return void
     */
    public function addUser(User $user)
    {
        if ($this->collUsers === null) {
            $this->initUsers();
        }
        if (!$this->collUsers->contains($user)) { // only add it if the **same** object is not already associated
            $this->doAddUser($user);

            $this->collUsers[]= $user;
        }
    }

    /**
     * @param	User $user The user object to add.
     */
    protected function doAddUser($user)
    {
        $watchlist = new Watchlist();
        $watchlist->setUser($user);
        $this->addWatchlist($watchlist);
    }

    /**
     * Remove a User object to this object
     * through the watchlist cross reference table.
     *
     * @param      User $user The Watchlist object to relate
     * @return void
     */
    public function removeUser(User $user)
    {
        if ($this->getUsers()->contains($user)) {
            $this->collUsers->remove($this->collUsers->search($user));
            if (null === $this->usersScheduledForDeletion) {
                $this->usersScheduledForDeletion = clone $this->collUsers;
                $this->usersScheduledForDeletion->clear();
            }
            $this->usersScheduledForDeletion[]= $user;
        }
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->data_id = null;
        $this->type_id = null;
        $this->name = null;
        $this->gem_store_description = null;
        $this->gem_store_blurb = null;
        $this->restriction_level = null;
        $this->rarity = null;
        $this->vendor_sell_price = null;
        $this->vendor_price = null;
        $this->karma_price = null;
        $this->img = null;
        $this->rarity_word = null;
        $this->item_type_id = null;
        $this->item_sub_type_id = null;
        $this->max_offer_unit_price = null;
        $this->min_sale_unit_price = null;
        $this->offer_availability = null;
        $this->sale_availability = null;
        $this->gw2db_id = null;
        $this->gw2db_external_id = null;
        $this->last_price_changed = null;
        $this->sale_price_change_last_hour = null;
        $this->offer_price_change_last_hour = null;
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
            if ($this->collResultOfRecipes) {
                foreach ($this->collResultOfRecipes as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collIngredients) {
                foreach ($this->collIngredients as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collSellListings) {
                foreach ($this->collSellListings as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collBuyListings) {
                foreach ($this->collBuyListings as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collOnWatchlists) {
                foreach ($this->collOnWatchlists as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collRecipes) {
                foreach ($this->collRecipes as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collUsers) {
                foreach ($this->collUsers as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        if ($this->collResultOfRecipes instanceof PropelCollection) {
            $this->collResultOfRecipes->clearIterator();
        }
        $this->collResultOfRecipes = null;
        if ($this->collIngredients instanceof PropelCollection) {
            $this->collIngredients->clearIterator();
        }
        $this->collIngredients = null;
        if ($this->collSellListings instanceof PropelCollection) {
            $this->collSellListings->clearIterator();
        }
        $this->collSellListings = null;
        if ($this->collBuyListings instanceof PropelCollection) {
            $this->collBuyListings->clearIterator();
        }
        $this->collBuyListings = null;
        if ($this->collOnWatchlists instanceof PropelCollection) {
            $this->collOnWatchlists->clearIterator();
        }
        $this->collOnWatchlists = null;
        if ($this->collRecipes instanceof PropelCollection) {
            $this->collRecipes->clearIterator();
        }
        $this->collRecipes = null;
        if ($this->collUsers instanceof PropelCollection) {
            $this->collUsers->clearIterator();
        }
        $this->collUsers = null;
        $this->aItemType = null;
        $this->aItemSubType = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ItemPeer::DEFAULT_STRING_FORMAT);
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

} // BaseItem
