<?php

use GW2Spidy\QueueManager\QueueManager;

require dirname(__FILE__) . '/../config/config.inc.php';
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