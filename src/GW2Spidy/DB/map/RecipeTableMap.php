<?php

namespace GW2Spidy\DB\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'recipe' table.
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
class RecipeTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'gw2spidy.map.RecipeTableMap';

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
        $this->setName('recipe');
        $this->setPhpName('Recipe');
        $this->setClassname('GW2Spidy\\DB\\Recipe');
        $this->setPackage('gw2spidy');
        $this->setUseIdGenerator(false);
        // columns
        $this->addPrimaryKey('DATA_ID', 'DataId', 'INTEGER', true, null, null);
        $this->addColumn('NAME', 'Name', 'VARCHAR', true, 255, null);
        $this->addForeignKey('DISCIPLINE_ID', 'DisciplineId', 'INTEGER', 'discipline', 'ID', false, null, null);
        $this->addColumn('RATING', 'Rating', 'INTEGER', false, 4, 0);
        $this->addForeignKey('RESULT_ITEM_ID', 'ResultItemId', 'INTEGER', 'item', 'DATA_ID', false, null, null);
        $this->addColumn('COUNT', 'Count', 'INTEGER', false, 4, 1);
        $this->addColumn('COST', 'Cost', 'INTEGER', false, null, null);
        $this->addColumn('SELL_PRICE', 'SellPrice', 'INTEGER', false, null, null);
        $this->addColumn('PROFIT', 'Profit', 'INTEGER', false, null, null);
        $this->addColumn('UPDATED', 'Updated', 'TIMESTAMP', false, null, null);
        $this->addColumn('REQUIRES_UNLOCK', 'RequiresUnlock', 'INTEGER', true, null, 0);
        $this->addColumn('GW2DB_ID', 'Gw2dbId', 'INTEGER', false, null, null);
        $this->addColumn('GW2DB_EXTERNAL_ID', 'Gw2dbExternalId', 'INTEGER', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Discipline', 'GW2Spidy\\DB\\Discipline', RelationMap::MANY_TO_ONE, array('discipline_id' => 'id', ), null, null);
        $this->addRelation('ResultItem', 'GW2Spidy\\DB\\Item', RelationMap::MANY_TO_ONE, array('result_item_id' => 'data_id', ), null, null);
        $this->addRelation('Ingredient', 'GW2Spidy\\DB\\RecipeIngredient', RelationMap::ONE_TO_MANY, array('data_id' => 'recipe_id', ), null, null, 'Ingredients');
        $this->addRelation('Item', 'GW2Spidy\\DB\\Item', RelationMap::MANY_TO_MANY, array(), null, null, 'Items');
    } // buildRelations()

} // RecipeTableMap
