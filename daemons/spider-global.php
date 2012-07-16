<?php

use GW2Spidy\DB\RequestWorkerQueue;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

$queueItem = new RequestWorkerQueue();
$queueItem->setWorker("\\GW2Spidy\\Spider\\ItemDBSpider");
$queueItem->save();