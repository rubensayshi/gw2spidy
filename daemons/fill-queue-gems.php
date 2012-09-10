<?php

use GW2Spidy\Queue\QueueManager;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

$manager = new QueueManager();

/*
 * build gem-exchange DB
 */
$manager->buildGemExchangeDB();