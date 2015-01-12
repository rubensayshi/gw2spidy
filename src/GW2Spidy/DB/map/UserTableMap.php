<?php

namespace GW2Spidy\DB\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'user' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.gw2spidy.map
 */
class UserTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'gw2spidy.map.UserTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('user');
        $this->setPhpName('User');
        $this->setClassname('GW2Spidy\\DB\\User');
        $this->setPackage('gw2spidy');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('USERNAME', 'Username', 'VARCHAR', true, 255, null);
        $this->addColumn('EMAIL', 'Email', 'VARCHAR', true, 255, null);
        $this->addColumn('PASSWORD', 'Password', 'VARCHAR', false, 255, null);
        $this->addColumn('ROLES', 'Roles', 'VARCHAR', false, 255, 'USER_ROLE');
        $this->addColumn('HYBRID_AUTH_PROVIDER_ID', 'HybridAuthProviderId', 'VARCHAR', false, 50, null);
        $this->addColumn('HYBRID_AUTH_ID', 'HybridAuthId', 'VARCHAR', false, 255, null);
        $this->addColumn('RESET_PASSWORD', 'ResetPassword', 'VARCHAR', false, 255, '');
        // validators
        $this->addValidator('USERNAME', 'unique', 'propel.validator.UniqueValidator', '', 'Username already exists!');
        $this->addValidator('EMAIL', 'unique', 'propel.validator.UniqueValidator', '', 'E-mail already exists!');
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Watchlist', 'GW2Spidy\\DB\\Watchlist', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), null, null, 'Watchlists');
        $this->addRelation('Item', 'GW2Spidy\\DB\\Item', RelationMap::MANY_TO_MANY, array(), null, null, 'Items');
    } // buildRelations()

} // UserTableMap
