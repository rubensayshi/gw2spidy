<?php

use GW2Spidy\Queue\QueueManager;
use GW2Spidy\DB\ItemTypeQuery;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

$manager = new QueueManager();

/*
 * build item DB, atm that also builds listingDB
 */
$manager->buildItemDB(true, ItemTypeQuery::create()->findOneByTitle('Weapon'));
