<?php

use GW2Spidy\Queue\QueueManager;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

$manager = new QueueManager();

/*
 * build Item DB, but only the first slice
 *  we're doing this so that any sudden major changes will alert us in time
 */
!in_array('--debug', $argv) && $manager->buildItemDB(false);

/*
 * build Listings DB
 */
$manager->buildListingsDB();