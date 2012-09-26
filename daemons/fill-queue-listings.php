<?php

use GW2Spidy\Queue\QueueManager;


require dirname(__FILE__) . '/../autoload.php';

$manager = new QueueManager();

/*
 * build item DB, atm that also builds listingDB
 */
$manager->buildListingsDB();