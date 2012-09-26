<?php

use GW2Spidy\Queue\QueueManager;


require dirname(__FILE__) . '/../autoload.php';

$manager = new QueueManager();

/*
 * build ItemType DB
 */
$manager->buildItemTypeDB();

/*
 * build Item DB
 */
$manager->buildItemDB(true);