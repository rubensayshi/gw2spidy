<?php

use GW2Spidy\DB\WorkerQueueItem;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

$queueItem = new WorkerQueueItem();
$queueItem->setWorker("\\GW2Spidy\\Spider\\ItemDBSpider");
$queueItem->save();