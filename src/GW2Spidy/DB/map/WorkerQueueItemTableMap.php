<?php

namespace GW2Spidy\DB\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'worker_queue_item' table.
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
class WorkerQueueItemTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'gw2spidy.map.WorkerQueueItemTableMap';

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
        $this->setName('worker_queue_item');
        $this->setPhpName('WorkerQueueItem');
        $this->setClassname('GW2Spidy\\DB\\WorkerQueueItem');
        $this->setPackage('gw2spidy');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('PRIORITY', 'Priority', 'INTEGER', false, null, 1);
        $this->addColumn('STATUS', 'Status', 'VARCHAR', true, 45, '');
        $this->addColumn('WORKER', 'Worker', 'VARCHAR', true, 255, '');
        $this->addColumn('RAW_DATA', 'RawData', 'CLOB', true, 255, '');
        $this->addColumn('HANDLER_UUID', 'HandlerUUID', 'VARCHAR', true, 255, '');
        $this->addColumn('TOUCHED', 'Touched', 'TIMESTAMP', false, null, null);
        $this->addColumn('MAX_TIMEOUT', 'MaxTimeout', 'INTEGER', true, null, 3600);
        $this->addColumn('LAST_LOG', 'LastLog', 'CLOB', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // WorkerQueueItemTableMap
